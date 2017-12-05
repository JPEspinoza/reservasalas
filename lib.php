<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * 
 *
 * @package    local
 * @subpackage reservasalas
 * @copyright  2014 Francisco García Ralph (francisco.garcia.ralph@gmail.com)
 * 					Nicolás Bañados Valladares (nbanados@alumnos.uai.cl)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//Libreria del plugin
//Se incluye automaticamente al llamar config.php
//function library, automatically included with by config.php

//Definir aqui las funciones que se usaran en varias paginas
defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");
require_once(dirname(__FILE__) . '/../../config.php'); //obligatorio

//Usada por Diego
function hora_modulo($modulo) {
	global $DB,$USER;
	//en formato HH:MM:SS (varchar)
	//Define las horas para misreservas.php

	$modulos=$DB->get_record('reservasalas_modulos',array("id"=>$modulo));
	
	$inicio=explode(":",$modulos->hora_inicio);
	$fin=explode(":",$modulos->hora_fin);
	
	$a=$inicio[0];$b=$inicio[1];$c=00; //hora;minuto;segundo
	$d=$fin[0];$e=(int)$fin[1];$f=00; //hora;minuto;segundo
	$minutos = str_replace(' ', '',$e);
	
	$ModuloInicia = new DateTime();
	// Se deja hora y minutos en 0 (medianoche)
	$ModuloInicia->setTime($a,$b,0);
	$ModuloTermina = new DateTime();
	// Se deja hora y minutos en 0 (medianoche)
	$ModuloTermina->setTime($d,$e,0);
$hora=array(
		$ModuloInicia,
		$ModuloTermina
);
//var_dump($hora);
	return $hora;
	//devuelve $hora[incio] y $hora[fin]
}
function modulo_hora($unixtime, $factor = null){

	$hora = date('G', $unixtime);
	$minuto = date('i', $unixtime);
	$segundo = $hora*60*60 + $minuto*60;
	if($factor== null){
		$factor = 15*60;
	}
	
	if($segundo > 19*60*60 + 10*60 +$factor){
		return 8;
	}else if($segundo > 15*60*60 + 40*60 + $factor){
		return 7;
	}else if($segundo > 16*60*60 + 10*60 + $factor){
		return 6;
	}else if($segundo > 14*60*60 + 10*60 + $factor){
		return 5;
	}else if($segundo > 12*60*60 + 40*60 + $factor){
		return 4;
	}else if($segundo > 11*60*60 + 30*60 + $factor){
		return 3;
	}else if($segundo > 10*60*60 + 0*60 + $factor){
		return 2;
	}else if($segundo > 8*60*60 + 30*60 + $factor){
		return 1;
	}else{
		return 0;
	}
}

function booking_availability($date){
	global $DB,$USER,$CFG;
	//format YYYY-MM-DD
	$today = date('Y-m-d',time());
	if( !$isbloked = $DB->get_record('reservasalas_bloqueados', array("alumno_id"=>$USER->id, 'estado'=>1))){
		
		$sqlWeekBookings = "SELECT *
						FROM {reservasalas_reservas}
						WHERE fecha_reserva >= ?
						AND fecha_reserva <= ADDDATE(?, 7)
						AND alumno_id = ? AND activa = 1";
	
		$weekBookings = $DB->get_records_sql($sqlWeekBookings, array($today, $today, $USER->id));
		$todayBookings = $DB->count_records ( 'reservasalas_reservas', array (
				'alumno_id' => $USER->id,
				'fecha_reserva' => date('Y-m-d',$date),
				'activa' => 1));
	
		$books= array(count($weekBookings),$todayBookings);

	}else{
		$books = array($CFG->reservasSemana,$CFG->reservasDia);
	}
	return $books;
}

function reservasalas_modal_rules() {
	return '<div id="myModal" class="modal fade" role="dialog" style="width: 75%; margin-left: -35%; z-index: -10;display:none;">
  				<div class="modal-dialog">
				    <div class="modal-content">
      					<div class="modal-header">
        					<button type="button" class="close" data-dismiss="modal">&times;</button>
        					<h4 class="modal-title">'.get_string("rules_title", "local_reservasalas").'</h4>
      					</div>
      					<div class="modal-body">
        					<p>'.get_string("rules_content", "local_reservasalas").'</p>
      					</div>
      					<div class="modal-footer">
	        				<button type="button" class="btn btn-default" data-dismiss="modal">'.get_string("close", "local_reservasalas").'</button>
    	  				</div>
    				</div>
  				</div>
			</div>';
}
