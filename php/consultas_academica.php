<?php
require_once('MisConsultas.php');
class consultas_academica
{
	function get_anios_academicos()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT anio_academico, anio_academico FROM sga_anio_academico WHERE anio_academico > 2000 ORDER BY 1 DESC";
		
		$anios = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $anios;
	}

	function get_nombres_cuatrimestre()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT periodo_lectivo, periodo_lectivo FROM sga_per_lect_gen WHERE tipo_de_periodo = 'Cuatrimestral'";
		
		$periodos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $periodos;
	}
	
	function get_docentes_comision($anio_academico, $cuatrimestre)
	{
		$db = MisConsultas::getConexion ();
		
		$sql = "SELECT C.comision, C.nombre AS nombre_comision, M.materia, M.nombre, D.legajo, P.apellido || ', ' || P.nombres AS docente, R.descripcion AS responsabilidad
					FROM sga_comisiones C
					JOIN sga_materias M ON (C.materia = M.materia)
					LEFT JOIN sga_docentes_com DC ON (DC.comision = C.comision)
					LEFT JOIN sga_docentes D ON (DC.legajo = D.legajo)
					LEFT JOIN sga_personas P ON (P.nro_inscripcion = D.nro_inscripcion)
					LEFT JOIN sga_responsab_doc R ON (DC.responsabilidad = R.responsabilidad)
					WHERE C.anio_academico = $anio_academico AND C.periodo_lectivo = '$cuatrimestre'
					ORDER BY M.nombre, P.apellido";

		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $resultado;
	}

	function get_sedes()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT sede, nombre as nombre_sede
				FROM sga_sedes";
		return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

}

?>