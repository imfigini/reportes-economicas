<?php
require_once('MisConsultas.php');

class dt_prueba extends toba_datos_tabla
{
	function get_alumnos_por_localidad($filtro=array())
	{
		$resultado = MisConsultas::getAlumnosLocalidad($filtro);
		
		return $resultado;
	}

	// function get_listado_nuevos_inscriptos($filtro=array())
	// {
	// 	$resultado = MisConsultas::getDatosNuervosInscriptos($filtro);
		
	// 	return $resultado;
	// }
	
	function get_listado_mails($filtro=array())
	{
		$resultado = MisConsultas::getDatosMails($filtro);
		return $resultado;
	}
	
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['descripcion'])) {
			$where[] = "descripcion ILIKE ".quote("%{$filtro['descripcion']}%");
		}
		$sql = "SELECT
			t_p.id,
			t_p.descripcion
		FROM
			prueba as t_p
		ORDER BY descripcion";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('Reportes')->consultar($sql);
	}













	function get_informacion_DCAAS($filtro=array())
	{
		$where = array();
		$whereOr = array();
		
		if (isset($filtro['descripcion'])) {
			$busqueda = strtoupper($filtro['descripcion']);
			$busqueda = quote("%{$busqueda}%");
			$whereOr[] = "(UPPER(nombres) LIKE " . $busqueda . ")";
			$whereOr[] = "(UPPER(apellido) LIKE " . $busqueda . ")";
			$whereOr[] = "(UPPER(M.nombre) LIKE " . $busqueda . ")";
			
			$where[] = "(". implode(' OR ', $whereOr) . ")";
		}
		$sql = "SELECT (P.apellido || ', ' || P.nombres) AS alumno,
												 
					   M.materia,
					   M.nombre AS materia_nombre,
					   V.nota,
					   V.fecha,
					   V.resultado,
					   MP.credito,
					   V.acta,
					   V.nro_resolucion AS resolucion
				FROM vw_hist_academica V,
					 sga_alumnos A,
					 sga_personas P,
					 sga_carreras C,
					 sga_materias M,
					 sga_atrib_mat_plan MP
				WHERE V.legajo = A.legajo
							 
				  AND V.carrera = A.carrera
				  AND A.nro_inscripcion = P.nro_inscripcion
				  AND V.carrera = C.carrera
				  AND V.materia = M.materia
				  AND MP.carrera = V.carrera
				  AND MP.plan = V.plan
				  AND MP.version = V.version
				  AND MP.materia = V.materia
				  AND C.carrera = 'DCAAS'";
		
		
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
	
		$sql .= " ORDER BY alumno, fecha";
		
		$resultado = MisConsultas::queryPosgrado($sql);
		
		return $resultado;
	}




	// function get_ingresantes($filtro=array())
	// {
	// 	$where = array();
		
	// 	if (isset($filtro['carrera'])) {
	// 		$where[] = "sga_alumnos.carrera LIKE ".quote("%{$filtro['carrera']}%");
	// 	}
		
	// 	if (isset($filtro['ingreso'])) {
	// 		$where[] = "sga_periodo_insc.anio_academico = ".$filtro['ingreso'];
	// 	}

	// 	$sql = 
	// 		'SELECT 
	// 			sga_sedes.nombre AS sede,
    //                             sga_carreras.nombre AS carrera, 
    //                             sga_personas.apellido || ", " || sga_personas.nombres AS nombre, 
	// 			sga_personas.nro_documento, 
    //                             sga_periodo_insc.anio_academico AS ingreso
	// 		FROM 
	// 			sga_personas, sga_alumnos, sga_carrera_aspira, sga_periodo_insc, sga_carreras, sga_sedes
	// 		WHERE 
    //                             sga_personas.nro_inscripcion = sga_alumnos.nro_inscripcion AND
	// 			sga_alumnos.nro_inscripcion = sga_carrera_aspira.nro_inscripcion AND
	// 			sga_alumnos.carrera = sga_carrera_aspira.carrera AND 
	// 			sga_carrera_aspira.periodo_inscripcio = sga_periodo_insc.periodo_inscripcio AND
	// 			sga_carrera_aspira.carrera = sga_carreras.carrera AND
    //                             sga_sedes.sede = sga_alumnos.sede AND
	// 			sga_alumnos.fecha_ingreso <= ALL (SELECT fecha_ingreso 
    //                                                                         FROM sga_alumnos A2 
    //                                                                         WHERE sga_alumnos.legajo = A2.legajo)';
				
	// 	if (count($where)>0) {
	// 		$sql = sql_concatenar_where($sql, $where);
	// 	}
                
    //             $sql .= " ORDER BY 5,2,3 ";

	// 	$resultado = MisConsultas::query($sql);
		
	// 	return $resultado;
		
	// }	

	function get_carreras() {
		$sql = "SELECT carrera, nombre FROM sga_carreras";
		
		$resultado = MisConsultas::query($sql);
		
		return $resultado;
	}
	
	function get_anios_ingreso() {
		$sql = "SELECT anio_academico::INT as ANIO_ACADEMICO FROM sga_anio_academico ORDER BY ANIO_ACADEMICO DESC";
		
		$resultado = MisConsultas::query($sql);
		
		return $resultado;
	}
	
}
?>