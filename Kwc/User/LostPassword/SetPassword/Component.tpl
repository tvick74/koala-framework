<div class="<?=$this->cssClass?>">
    <h1><?=$this->data->trlKwf('Set a new password')?></h1>
    <p>
        <?=$this->data->trlKwf('Plese enter in both fields the password which you want to use for your useraccount')?>.<br />
        <?=$this->data->trlKwf('After entering your new password you are automatically logged in.')?>
    </p>

    <?=$this->component($this->form)?>
</div>