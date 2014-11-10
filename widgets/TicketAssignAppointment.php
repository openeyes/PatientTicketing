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

namespace OEModule\PatientTicketing\widgets;

use OEModule\PatientTicketing\models;

class TicketAssignAppointment extends BaseTicketAssignment {
	/**
	 * Extract form data for storing in assignment table
	 *
	 * @param $form_data
	 * @return array|void
	 */
	public function extractFormData($form_data)
	{
		$res = array();
		foreach (array('appointment_date', 'appointment_time') as $k) {
			$res[$k] = @$form_data[$k];
		}
		return $res;
	}

	/**
	 * Perform form data validation
	 *
	 * @param $form_data
	 * @return array
	 */
	public function validate($form_data)
	{

		$errs = array();
		if (!@$form_data['appointment_date']) {
			$errs['appointment_date'] = "Please enter an appointment date";
		}

		if (!@$form_data['appointment_time']) {
			$errs['appointment_time'] = "Please enter an appointment time";
		}

		$appointment_date = \Helper::convertNHS2MySQL($form_data['appointment_date']);
		$date_validator = new \OEDateValidator();
		if(!$date_validator->validateValue($appointment_date)){
			if(strtotime($appointment_date)!=false){
				$errs['appointment_date'] = 'Appointment date is not in valid format';
			}
			else {
				$errs['appointment_date'] = 'Appointment date is not a valid date';
			}
		}

		$appointment_time = $form_data['appointment_time'];
		if(!$this->isValidTimeValue($appointment_time)){
			$errs['appointment_time'] = 'Appointment time is not valid';
		}

		return $errs;
	}

	public function isValidTimeValue($value)
	{
		if (!preg_match("/^(([01]?[0-9])|(2[0-3])):[0-5][0-9]$/", $value)) {
			return false;
		}
		return true;
	}

	/**
	 * Generate string from the widget captured data
	 *
	 * @param $data
	 * @return string|void
	 */
	public function getReportString($data)
	{
		$res = "Follow-up appointment booked for " . @$data['appointment_date'] . " at " . @$data['appointment_time'];
		return $res;
	}

}