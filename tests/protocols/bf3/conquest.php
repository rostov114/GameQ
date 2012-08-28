<?php
/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Battlefield 3 Protocol Test Class
 *
 * @author Austin Bischoff <austin@codebeard.com>
 */
class Tests_Protocols_Bf3_Conquest extends Tests_Protocols_Bf3
{
	/**
	 * @dataProvider provider_status
	 */
	public function test_status($actual)
	{
		// Define expected result here
		$expected = array(
			'dedicated' => TRUE,
			'mod' => FALSE,
			'hostname' => 'KGB 1000 ticket 24/7 Grand Bazaar',
			'numplayers' => 48,
			'maxplayers' => 64,
			'gametype' => 'ConquestLarge0',
			'map' => 'MP_001',
			'roundsplayed' => 1,
			'roundstotal' => 2,
			'teams' => array(
				array(
					'tickets' => 1004.396,
					'id' => 1,
				),
				array(
					'tickets' => 991.119141,
					'id' => 2,
				),
			),
			'targetscore' => '0',
			'online' => FALSE,
			'ranked' => TRUE,
			'punkbuster' => TRUE,
			'password' => TRUE,
			'uptime' => 391828,
			'roundtime' => 177,
			'ip_port' => '',
			'punkbuster_version' => '',
			'join_queue' => FALSE,
			'region' => 'NAm',
			'pingsite' => 'iad',
			'country' => 'US',
		);

		$this->assertEquals($expected, $actual);
	}
}
