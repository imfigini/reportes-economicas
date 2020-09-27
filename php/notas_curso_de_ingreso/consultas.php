<?php
require_once('MisConsultas.php');

class consultas
{
	static function get_anio_ingreso()
	{
		$db = MisConsultas::getConexion ();
		$sqlText = "SELECT DISTINCT anio_ingreso
                                FROM curso_ingreso
                            ORDER BY anio_ingreso DESC";
		
		$anios = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
		return $anios;
	}

	static function get_alumnos($filtro=array())
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT anio_ingreso, apellido || ', ' || nombres AS alumno, tipo_documento, nro_documento, fecha_de_examen, forma_aprobacion, resultado, nota 
					FROM curso_ingreso";
		
		$where = array();
		if (isset($filtro['anio'])) 
			$where[] = "anio_ingreso = ".$filtro['anio'];
		if (isset($filtro['nombre'])) 
			$where[] = "apellido LIKE ".quote("%{$filtro['nombre']}%")." OR nombres LIKE ".quote("%{$filtro['nombre']}%");
		if (count($where)>0) 
			$sql = sql_concatenar_where($sql, $where);

		$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); 
		return $alumnos;
	}
}
