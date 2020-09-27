<?php
require_once ('MisConsultas.php');

class ci_ciudad_de_procedencia extends toba_ci
{

	//---- Cuadro -----------------------------------------------------------------------
	
	function get_alumnos_con_procedencia()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT 
						A.legajo, 
						P.apellido || ', ' || P.nombres AS alumno, 
						PC.nombre AS prov_colegio,
						DPC.nombre AS partido_colegio, 
						LC.nombre AS localidad_colegio, 
						PP.nombre AS prov_nacimiento,
						DPP.nombre AS partido_nacimiento,
						LP.nombre AS localidad_nacimiento, 						
						C1.nombre_reducido AS carrera_1, 
						C2.nombre_reducido AS carrera_2, 
						C3.nombre_reducido AS carrera_3
					FROM rep_alumno_carreras R
					JOIN sga_alumnos A ON (R.legajo = A.legajo AND A.regular = 'S' AND A.calidad = 'A')
					JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
					LEFT JOIN sga_coleg_sec C ON (P.colegio_secundario = C.colegio)
					LEFT JOIN mug_localidades LC ON (C.localidad = LC.localidad)
					LEFT JOIN mug_dptos_partidos DPC ON (LC.dpto_partido = DPC.dpto_partido)
					LEFT JOIN mug_provincias PC ON (PC.provincia = DPC.provincia)
					LEFT JOIN mug_localidades LP ON (P.loc_nacimiento = LP.localidad)
					LEFT JOIN mug_dptos_partidos DPP ON (LP.dpto_partido = DPP.dpto_partido)
					LEFT JOIN mug_provincias PP ON (PP.provincia = DPP.provincia)
					LEFT JOIN sga_carreras C1 ON (R.carrera_1 = C1.carrera)
					LEFT JOIN sga_carreras C2 ON (R.carrera_2 = C2.carrera)
					LEFT JOIN sga_carreras C3 ON (R.carrera_3 = C3.carrera)
				ORDER BY alumno";
		
		$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_NUM
		//ei_arbol($alumnos); die;
		return $alumnos;
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_alumnos_con_procedencia();
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
/*
	function evt__procesar()
	{
		$db = MisConsultas::getConexion ();
		$sql = 'EXECUTE PROCEDURE sp_rep_alu_carreras()';
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_NUM);  
		return $fecha;
	}
*/
	//-----------------------------------------------------------------------------------
	//---- ultima_fecha_actualiz --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function get_ultima_fecha_modificacion()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT MAX(fecha_actualiz) AS fecha FROM rep_fecha_ultima_actualiz
					WHERE tabname = 'rep_alumno_carreras'";
		
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_NUM
		//ei_arbol($fecha); 
		return $fecha[0]['FECHA'];
	}
	
	function evt__ultima_fecha_actualiz__procesar($datos)
	{
		$db = MisConsultas::getConexion ();
		$sql = 'EXECUTE PROCEDURE sp_rep_alu_carreras()';
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_NUM);  
		return $fecha;
	}

}
?>