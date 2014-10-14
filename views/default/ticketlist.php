<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<?php
$can_process = $queueset && $qs_svc->isQueueSetPermissionedForUser($queueset, Yii::app()->user->id);
?>
	<!--
	<div class="panel panel actions row">

		<div class="large-12 column">
			<?php if ($this->checkAccess('OprnPrint')) {?>
				<div class="button-bar">
					<button id="btn_print" class="small">Print</button>
				</div>
			<?php }?>
		</div>
	</div>
	-->

	<div class="search-filters theatre-diaries">
		<?php $this->beginWidget('CActiveForm', array(
						'id'=>'ticket-filter',
						'htmlOptions'=>array(
								'class' => 'row'
						),
						'enableAjaxValidation'=>false,
				))?>
		<input type="hidden" name="queueset_id" value="<?=$queueset->getId()?>" />
		<?php
		$filter_settings = $queueset->filter_settings;
		?>
		<div class="large-12 column">
			<div class="panel">
				<div class="row">
					<div class="large-8 column">
						<table class="grid">
							<thead>
							<tr>
								<th>Patient List:</th>
								<?php if ($filter_settings->priority) {?>
									<th>Priority:</th>
								<?php }?>
								<?php if ($filter_settings->subspecialty) {?>
									<th>Subspecialty:</th>
								<?php }?>
								<?php if ($filter_settings->firm) {?>
									<th>Firm:</th>
								<?php }?>
								<?php if ($filter_settings->my_tickets) {?>
									<th nowrap>My Tickets:</th>
								<?php }?>
								<?php if ($filter_settings->closed_tickets) {?>
									<th nowrap>Closed Tickets:</th>
								<?php }?>
								<th>&nbsp;</th>
							</tr>
							</thead>
							<tbody>
							<tr class="filter-row">
								<td>
									<?php
									$this->widget('application.widgets.MultiSelectList', array(
											'field' => 'queue-ids',
											'default_options' => @$_POST['queue-ids'],
											'options' => CHtml::listData($qs_svc->getQueueSetQueues($queueset, false),'id','name'),
											'htmlOptions' => array('empty' => '- Please Select -', 'nowrapper' => true),
											'noSelectionsMessage' => 'All Patient Lists')
											);
									?>

								</td>
								<?php if ($filter_settings->priority) {?>
								<td>
									<?php $this->widget('application.widgets.MultiSelectList', array(
													'field' => 'priority-ids',
													'default_options' => @$_POST['priority-ids'],
													'options' => CHtml::listData(OEModule\PatientTicketing\models\Priority::model()->findAll(), 'id', 'name'),
													'htmlOptions' => array('empty' => '- Please Select -', 'nowrapper' => true),
													'noSelectionsMessage' => 'All Priorities')
									) ?>
								</td>
								<?php }?>
								<?php if ($filter_settings->subspecialty) {?>
								<td>
									<?php echo CHtml::dropDownList('subspecialty-id', @$_POST['subspecialty-id'], Subspecialty::model()->getList(), array('empty'=>'All specialties', 'disabled' => (@$_POST['emergency_list']==1 ? 'disabled' : '')))?>
								</td>
								<?php }?>
								<?php if ($filter_settings->firm) {?>
								<td>
									<?php if (!@$_POST['subspecialty-id']) {?>
										<?php echo CHtml::dropDownList('firm-id', '', array(), array('empty'=>'All firms', 'disabled' => 'disabled'))?>
									<?php } else {?>
										<?php echo CHtml::dropDownList('firm-id', @$_POST['firm-id'], Firm::model()->getList(@$_POST['subspecialty-id']), array('empty'=>'All firms', 'disabled' => (@$_POST['emergency_list']==1 ? 'disabled' : '')))?>
									<?php }?>
								</td>
								<?php }?>
								<?php if ($filter_settings->my_tickets) {?>
								<td align="middle">
									<?php echo CHtml::checkBox('my-tickets', (@$_POST['my-tickets'] == 1))?>
								</td>
								<?php }?>
								<?php if ($filter_settings->closed_tickets) {?>
								<td align="middle">
									<?php echo CHtml::checkBox('closed-tickets', (@$_POST['closed-tickets'] == 1))?>
								</td>
								<?php }?>
								<td>
									<button id="search_button" class="secondary small" type="submit">
										<?php if ($patient_filter) {?>Apply<? } else {?>Search<?php }?>
									</button>
								</td>
							</tr>
							</tbody>
						</table>

					</div>
				</div>
			</div>
		</div>
		<?php $this->endWidget()?>
	</div>
	<?php if ($patient_filter) {?>
		<div class="large-12 column">
			<div class="alert-box warning">Filtering for <?= $patient_filter->getFullName() ?></div>
		</div>
	<?php }?>

	<?php $this->renderPartial('_ticketlist', array('tickets' => $tickets, 'pages' => $pages, 'can_process' => $can_process)); ?>

<script type="text/html" id="ticketcontroller-queue-select-template">
	<form class="moveTicket" data-event-types='{{{event_types}}}' data-ticketinfo='{{{ticketInfo}}}'>
	<input type="hidden" name="YII_CSRF_TOKEN" value="{{CSRF_TOKEN}}" />
	<input type="hidden" name="from_queue_id" value="{{current_queue_id}}" />
	<div>
		<h2>Move {{patient_name}}</h2>
		<div>
		<fieldset class="field-row row">
			<div class="large-2 column">
			<label>From:</label>
			</div>
			<div class=large-9 column end"><div>{{current_queue_name}}</div></div>
		</fieldset>
		<fieldset class="field-row row">
			<div class="large-2 column">
				<label for="to_queue_id">To:</label>
			</div>
			<div class="large-6 column">
			<select name="to_queue_id" id="to_queue_id">
				<option value=""> - Please Select - </option>
				{{{outcome_options}}}
			</select>
			</div>
			<div class="large-1 column end">
				<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;">
			</div>
		</fieldset>
		</div>
		<div id="queue-assignment-placeholder"></div>
		<div class="alert-box alert hidden"></div>
		<div class="buttons">
			<button class="secondary small ok" type="button">OK</button>
			<button class="warning small cancel" type="button">Cancel</button>
		</div>
		<div class="event-types">
			{{{event_type_links}}}
		</div>
	</div>
	</form>
</script>
