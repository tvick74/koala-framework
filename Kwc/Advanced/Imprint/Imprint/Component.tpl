<div class="<?=$this->cssClass?>">
    <? if($this->row->company || $this->row->name || $this->row->address || $this->row->zipcode || $this->row->city) {?>
        <span class="imprintHeadline"><?=trlKwf('Operating company / responsible person for the content');?></span>
        <p>
            <? if($this->row->company) echo $this->row->company."<br/>";?>
            <? if($this->row->name) echo $this->row->name."<br/>";?>
            <? if($this->row->address) echo $this->row->address."<br/>";?>
            <? if($this->row->zipcode) echo $this->row->zipcode;?><? if($this->row->city) echo " ".$this->row->city;?>
        </p>
    <? } ?>
    <? if($this->row->fon || $this->row->fax || $this->row->mobile) {?>
        <p>
            <? if($this->row->fon) echo trlKwf('Fon').": ".$this->row->fon."<br/>";?>
            <? if($this->row->fax) echo trlKwf('Fax').": ".$this->row->fax."<br/>";?>
            <? if($this->row->mobile) echo trlKwf('Mobile').": ".$this->row->mobile;?>
        </p>
    <? } ?>
    <? if($this->row->email || $this->row->website) {?>
        <p>
            <? if($this->row->email) echo "<a href='mailto:".$this->row->email."'>".$this->row->email."</a><br/>"?>
            <? if($this->row->website) echo "<a href='".$this->row->website."' rel='popup_blank'>".$this->row->website."</a>"?>
        </p>
    <? } ?>
    <? if($this->row->crn) {?>
        <span class="imprintHeadline"><?=trlKwf('Commercial register number');?></span>
        <p>
            <?=$this->row->crn;?>
        </p>
    <? } ?>
    <? if($this->row->register_court) {?>
        <span class="imprintHeadline"><?=trlKwf('Register court');?></span>
        <p>
            <?=$this->row->register_court;?>
        </p>
    <? } ?>
    <? if($this->row->court) {?>
        <span class="imprintHeadline"><?=trlKwf('Court');?></span>
        <p>
            <?=$this->row->court;?>
        </p>
    <? } ?>
    <? if($this->row->uid_number) {?>
        <span class="imprintHeadline"><?=trlKwf('Purchase tax-identification number');?></span>
        <p>
            <?=$this->row->uid_number;?>
        </p>
    <? } ?>
    <? if($this->row->bank_data || $this->row->bank_code || $this->row->account_number || $this->row->iban || $this->row->bic_swift) {?>
        <span class="imprintHeadline"><?=trlKwf('Bank data');?></span>
        <p>
            <? if($this->row->bank_data) echo $this->row->bank_data."<br/>";?>
            <? if($this->row->bank_code) echo trlKwf('Bank code').": ".$this->row->bank_code."<br/>";?>
            <? if($this->row->account_number) echo trlKwf('Account number').": ".$this->row->account_number."<br/>";?>
            <? if($this->row->iban) echo trlKwf('IBAN').": ".$this->row->iban."<br/>";?>
            <? if($this->row->bic_swift) echo trlKwf('BIC / SWIFT').": ".$this->row->bic_swift;?>
        </p>
    <? } ?>
    <? if($this->row->dvr_number) {?>
        <span class="imprintHeadline"><?=trlKwf('DVR-Number');?></span>
        <p>
            <?=$this->row->dvr_number;?>
        </p>
    <? } ?>
    <? if($this->row->club_number_zvr) {?>
        <span class="imprintHeadline"><?=trlKwf('Clubnumber ZVR');?></span>
        <p>
            <?=$this->row->club_number_zvr;?>
        </p>
    <? } ?>
    <? if($this->row->job_title) {?>
        <span class="imprintHeadline"><?=trlKwf('Job title');?></span>
        <p>
            <?=$this->row->job_title;?>
        </p>
    <? } ?>
    <? if($this->row->agency) {?>
        <span class="imprintHeadline"><?=trlKwf('Agency accordant §5 ECG');?></span>
        <p>
            <?=$this->row->agency;?>
        </p>
    <? } ?>
    <? if($this->row->employment_specification) {?>
        <span class="imprintHeadline"><?=trlKwf('Employment specification');?></span>
        <p>
            <?=$this->row->employment_specification;?>
        </p>
    <? } ?>
    <? if($this->row->link_company_az) {?>
        <span class="imprintHeadline"><?=trlKwf('Entry at WK Austria');?></span>
        <p>
            <a href="<?=$this->row->link_company_az;?>" rel="popup_blank"><?=trlKwf('Company A-Z');?></a>
        </p>
    <? } ?>
</div>

