<div class="contentArea">
<?
    if ($isSent)
    {
         $this->widget('ContentBlockWidget', array('name' => 'request_sent'));
    }
    else
    {
        ?>
<?php $this->widget('ContentBlockWidget', array('name' => 'request_topText')); ?>

<form id="formJoin" accept-charset="utf-8" method="post" action="/request" novalidate="novalidate">
    <input type='hidden' name='sent' value='1'/>
    <div class="fieldContainer">

	<div class="formRow">
		<label for="name">Имя <span class="star">*</span></label>
		<input type="text" maxlength="30" id="name" name="name">
			</div>

	<div class="formRow">
		<label for="lastname">Фамилия <span class="star">*</span></label>
		<input type="text" maxlength="30" id="lastname" name="lastname">
			</div>

	<div class="formRow">
		<label for="country">Страна <span class="star">*</span></label>
		<select id="country" name="country">
        <?php foreach (CalcRequest::getCountryData () as $k=>$v) {
            echo '<option value="'.$k.'">'.$v.'</option>';
        }
        ?>
		</select>
			</div>

	<div class="formRow">
		<label for="city">Город</label>
		<input type="text" maxlength="50" id="city" name="city">
			</div>

	<div class="formRow">
		<label for="phone">Моб. телефон <span class="star">*</span><br></label>
		<input type="text" maxlength="30" id="phone" name="phone">
			</div>

	<div class="formRow">
		<label for="email">Email <span class="star">*</span><br><small>(корректный)</small></label>
		<input type="text" maxlength="50" id="email" name="email">
			</div>

	<div class="formRow">
		<label for="skype">Skype</label>
		<input type="text" maxlength="50" id="skype" name="skype">
			</div>

	<div class="formRow">
		<label for="icq">ICQ</label>
		<input type="text" maxlength="9" id="icq" name="icq">
			</div>

	<div class="formRow">
		<label for="information">Др. информация<br><small>(желательно указать примерную сумму вклада и сроки)</small></label>
		<textarea maxlength="1000" id="information" name="information"></textarea>
			</div>

</div>

<p class="formNote"><span class="star">*</span> &mdash; поля, обязательные для заполнения</p>

<div class="submitButton">
	<input type="submit" value="Отправить" id="submit">
</div>

</form>
        <?php
    }

?>
</div>