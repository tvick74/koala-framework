<div class="<?=$this->cssClass?>">
    <div class="webStandard">
        <div class="back"><?=$this->componentLink($this->userProfile, trlVps('Show my Profile'))?></div>
        <h1 class="mainHeadline"><?=trlVps('Account - Change Password')?></h1>
    </div>
    <?=$this->component($this->form)?>
</div>