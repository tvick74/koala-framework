<?php
class Vps_Controller_Action_Cli_CreateSvnIgnoreController_DirectoryFilter
    extends RecursiveFilterIterator
{
    private $it;

    public function __construct(DirectoryIterator $it)
    {
        parent::__construct($it);
        $this->it = $it;
    }

    public function accept()
    {
        if (!$this->it->isDir()) return false;
        if ($this->it->getFilename() == '.svn') return false;
        return true;
    }

}

class Vps_Controller_Action_Cli_CreateSvnIgnoreController_IgnoredFilter
    extends Vps_Controller_Action_Cli_CreateSvnIgnoreController_DirectoryFilter
{
    private $it;

    public function __construct(DirectoryIterator $it)
    {
        parent::__construct($it);
        $this->it = $it;
    }

    public function accept()
    {
        if (!parent::accept()) return false;
        $p = $this->it->getPathname();
        $st = simplexml_load_string(`svn st --depth empty  --xml $p`);
        $st = (string)$st->target->entry->{'wc-status'}['item'];
        if ($st == 'unversioned') {
            return false;
        }
        if ($st == 'ignored') {
            return false;
        }
        return true;
    }

}

class Vps_Controller_Action_Cli_CreateSvnIgnoreController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'erstellt svn-ignore eintraege fuer nicht vps-projekte';
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'dir',
                'help' => 'what to import'
            )
        );
    }

    public function indexAction()
    {
        $dir = $this->_getParam('dir');
        if (!$dir) {
            throw new Vps_ClientException("Parameter dir wird benoetigt");
        }
        foreach (new RecursiveIteratorIterator(
                    new Vps_Controller_Action_Cli_CreateSvnIgnoreController_DirectoryFilter(
                        new RecursiveDirectoryIterator($dir)),
                    RecursiveIteratorIterator::SELF_FIRST) as $d
        ) {
            $d = (string)$d;
            echo (string)$d."\n";

            $x = '';
            foreach (explode('/', $d) as $i) {
                if (!$x) {
                    $x = $i;
                    continue;
                }
                $x .= '/'.$i;
                $st = simplexml_load_string(`svn st --depth empty  --xml $x`);
                $st = (string)$st->target->entry->{'wc-status'}['item'];
                if ($st == 'unversioned') {
                    $this->_systemCheckRet("svn add --non-recursive $x");
                }
                if ($st == 'ignored') {
                    continue 2;
                }
            }

            $numeric = 0;
            $nonNumeric = array();
            $extensions = array();
            foreach (new DirectoryIterator($d) as $i) {
                if ($i->isDir()) continue;
                $i = (string)$i;
                if (strrpos($i, '.') == strlen($i)-1) {
                    echo "KEINE ENDUNG: $i\n";
                    continue;
                }
                $x = substr($i, -(strlen($i)-strrpos($i, '.')-1));
                $i = substr($i, 0, strrpos($i, '.'));
                //echo "'$i' . '$x'\n";
                if (is_numeric(str_replace('_', '', $i))) {
                    $numeric++;
                    if (!in_array($x, $extensions)) {
                        $extensions[] = $x;
                    }
                } else {
                    $nonNumeric[] = $i.'.'.$x;
                }
            }
//             p($numeric);
            if ($extensions) {
                $ignore = $this->_getSvnIgnore($d);
                $added = false;
                foreach ($extensions as $x) {
                    if (!in_array('*.'.$x, $ignore)) {
                        echo "      ---------> *.$x\n";
                        $ignore[] = '*.'.$x;

                        $added = true;
                    }
                    $st = simplexml_load_string(`svn st --xml $d/*.$x`);
                    foreach ($st->target as $t) {
                        if ($t->entry->{'wc-status'}['item'] == 'ignored') continue;
                        if ($t->entry->{'wc-status'}['item'] == 'unversioned') continue;
                        $cmd = "svn rm --force ".escapeshellarg((string)$t['path']);
                        echo $cmd."\n";
                        $this->_systemCheckRet($cmd);
                    }
                }
                if ($added) {
                    $this->_setSvnIgnore($d, $ignore);
                }
            }
            if ($nonNumeric) {
                if (preg_match('#/[^/]*(rte|download)[^/]*$#', $d)
                    || preg_match('#/[^/]*(rte|download)[^/]*/datei$#', $d))
                {
                    $ignore = $this->_getSvnIgnore($d);
                    if (!in_array('*', $ignore)) {
                        $ignore[] = '*';
                        $this->_setSvnIgnore($d, $ignore);
                    }
                } else {
                    foreach ($nonNumeric as $i) {
                        if ($i == 'Thumbs.db') {
                            $ignore = $this->_getSvnIgnore($d);
                            if (!in_array($i, $ignore)) {
                                $ignore[] = $i;
                                $this->_setSvnIgnore($d, $ignore);
                            }
                        } else {
                            echo "UNBEKANNTE DATEI: $i\n";
                        }
                    }
                }
            }
        }
        exit;
    }
    public function getIgnoresAction()
    {
        $dir = $this->_getParam('dir');
        if (!$dir) {
            throw new Vps_ClientException("Parameter dir wird benoetigt");
        }
        $ig = simplexml_load_string(`svn propget --recursive --xml svn:ignore $dir`);
        $ignores = array();
        foreach ($ig->target as $t) {
            $p = explode("\n", trim((string)$t->property));
            foreach ($p as $i) {
                $ignores[] = (string)$t['path'] . '/' . trim($i);
            }
        }

        $includes = array();
        foreach ($ignores as $i) {
            $p = '';
            foreach (explode('/', $i) as $j) {
                $p .= $j.'/';
                $e = trim($p, '/');
                if (substr($e, -1) == '*') $e .= '*';
                if (!in_array($e, $includes)) {
                    $includes[] = $e;
                }
            }
        }
        $cmd = 'rsync --progress --verbose --recursive --exclude=\'.svn\' ';
        foreach ($includes as $i) {
            $cmd .= "--include='$i' ";
        }
        $cmd .= "--exclude='*' ";
        $cmd .= '. /www/public/niko/vwtest';
        echo $cmd;
        passthru($cmd);
        exit;
    }

    private function _getSvnIgnore($dir)
    {
        $prop = simplexml_load_string(`svn propget svn:ignore $dir --xml`);
        if ($prop->target->property['name'] != 'svn:ignore') return array();
        $ret = array();
        foreach (explode("\n", (string)$prop->target->property) as $i) {
            $i = trim($i);
            if ($i) {
                $ret[] = $i;
            }
        }
        return $ret;
    }
    private function _setSvnIgnore($dir, $ignore)
    {
        if (in_array('*', $ignore)) $ignore = array('*');
        $ignore = implode("\n", array_unique($ignore));
        $ignore = escapeshellarg($ignore);
        $cmd = "svn propset svn:ignore $ignore $dir";
        echo $cmd."\n";
        $this->_systemCheckRet($cmd." >/dev/null");
    }
}