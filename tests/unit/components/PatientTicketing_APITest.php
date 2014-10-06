<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

use OEModule\PatientTicketing\models;

class PatientTicketing_APITest extends CDbTestCase
{
	private $api;

	public $fixtures = array(
			'queuesets' => 'OEModule\PatientTicketing\models\QueueSet',
			'queue' => 'OEModule\PatientTicketing\models\Queue'
	);

	static public function setupBeforeClass()
	{
		Yii::app()->getModule('PatientTicketing');
	}

	public function setUp()
	{
		parent::setUp();
		$this->orig_svcman = Yii::app()->service;
		$this->svcman = new ServiceManagerWrapper();

		Yii::app()->setComponent('service', $this->svcman);
		$this->api = Yii::app()->moduleAPI->get('PatientTicketing');
	}

	public function tearDown()
	{
		Yii::app()->setComponent('services', $this->orig_svcman);
		parent::tearDown();
	}

	public function testcanAddPatientToQueue()
	{
		$patient = new \Patient();
		$queue = new models\Queue();
		$queue->id = 5;

		$qs_r = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSet')
				->disableOriginalConstructor()
				->setMethods(array('getId'))
				->getMock();

		$qs_r->expects($this->any())
			->method('getId')
			->will($this->returnValue(3));

		$qs_svc = $this->getMockBuilder('OEModule\PatientTicketing\services\PatientTicketing_QueueSetService')
				->disableOriginalConstructor()
				->setMethods(array('canAddPatientToQueueSet', 'getQueueSetForQueue'))
				->getMock();

		$qs_svc->expects($this->once())
				->method('getQueueSetForQueue')
				->with(5)
				->will($this->returnValue($qs_r));

		$qs_svc->expects($this->once())
			->method('canAddPatientToQueueSet')
			->with($patient, 3)
			->will($this->returnValue('test return'));

		$this->svcman->mocked_services['PatientTicketing_QueueSet'] = $qs_svc;

		$this->assertEquals('test return', $this->api->canAddPatientToQueue($patient, $queue), "Should be passing through return value from service method");
	}

}

class ServiceManagerWrapper extends \services\ServiceManager
{
	public $mocked_services = array();

	public function getService($name)
	{
		if (@$this->mocked_services[$name]) {
			return $this->mocked_services[$name];
		}
		else {
			return parent::getService($name);
		}
	}
}
