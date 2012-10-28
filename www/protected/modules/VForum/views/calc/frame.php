	<h2>Сколько я заработаю в МММ-2011?</h2>
	<form id="formCalc" action="" method="post">
		<div class="fieldContainer">

			
			<div class="formRow">
				<label for="deposit">Сумма для покупки МАВРО</label>
				<input type="text" name="deposit" id="deposit" maxlength="8" />
				<select name="currency" id="currency">
					<option value="RUB" selected>RUB</option>
					<option value="USD">USD</option>
					<option value="EUR">EUR</option>

					<option value="UAH">UAH</option>
				</select>
			</div>
			
			<div class="formRow">
				<label for="period">Срок</label>
				<input type="text" name="period" maxlength="2" id="period" /><span class="field2">мес.</span>
			</div>

			
			<div class="formRow">
				<label for="percent">Темпы роста МАВРО</label>
				<input type="text" name="percent" id="percent" maxlength="2" value="40" /><span class="field2">% / мес.</span>
			</div>
			
		</div>
		<div class="submitButton">
<!--            <p class="formNote">Если калькулятор не работает, нажмите F5 (обновить страницу)</p> -->
			<input type="button" name="submit" id="submit" onclick="calcStart()" value="Рассчитать" />

			<input type="reset" name="reset" id="reset" value="Сброс" />
		</div>
	</form>
</div>			<div class="contentArea hide">
    <div id="result">

        <h2>Через <span id="periodView"></span></h2>
        <div class="fieldContainer" style="overflow: hidden;">
        <p>Ваш вклад возрастет до <span class="number" id="moneyView"></span> (увеличится в <span class="number" id="increaseView"></span> раз(-а))</p>

        <p>Чистый доход составит <span class="number" id="profitView"></span></p>
        <p>Вступив в систему сейчас, Вы бы уже имели: <span id="earnNow">Загрузка...</span></p>
<!--            <div id="earnNowView" style="padding: 10px; height: 150px;"> </div>
            <div style="clear: both;">&nbsp;</div> -->
        </div>

        <h2>Ваш доход:</h2>
        <div class="fieldContainer">
            <table class="info">

                <tbody><tr>
                        <th>в месяц</th>
                        <th>в день</th>
                        <th>в час</th>
                        <th>в минуту</th>
                    </tr>
                    <tr>

                        <td id="earnPerMonth"><span class="money"></span></td>
                        <td id="earnPerDay"><span class="money"></span></td>
                        <td id="earnPerHour"><span class="money"></span></td>
                        <td id="earnPerMinute"><span class="money"></span></td>
                    </tr>
                    <tr>
                        <td id="earnPerMonthView"></td>
                        <td id="earnPerDayView"></td>
                        <td id="earnPerHourView"></td>

                        <td id="earnPerMinuteView"></td>
                    </tr>
                </tbody></table>
        </div>
    </div>
</div>