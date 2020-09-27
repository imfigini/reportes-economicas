<?php
require_once('MisConsultas.php');
class consultas
{
	function get_alumnos_con_correlat_aprob($filtro=array())
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT A.legajo, P.apellido || ', ' || P.nombres AS alumno 
					FROM sga_alumnos A
						JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
					WHERE A.regular = 'S' AND A.calidad = 'A'
					ORDER BY alumno";
		
		$where = array();
		if (isset($filtro['carrera'])) 
		{
			$carrera = $filtro['carrera'];
			$where[] = "A.carrera = $carrera";
		}
		if (count($where)>0) 
			$sql = sql_concatenar_where($sql, $where);

		$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	
		$materia = $filtro['materia'];
		$alumnos = consultas::verificar_correlativas($alumnos, $carrera, $materia);
		return $alumnos;
	}
	
	function verificar_correlativas($alumnos, $carrera, $materia)
	{
		$db = MisConsultas::getConexion ();
		$max = count($alumnos);
		for ($i=0; $i<$max; $i++)
		{
			$legajo = $alumnos[$i]['LEGAJO'];
			$sql = "EXECUTE PROCEDURE sp_plan_de_alumno('EXA', $carrera, $legajo, CURRENT YEAR TO SECOND)";
			$plan_ver = $db->query($sql)->fetchAll(PDO::FETCH_NUM); 
			
			$plan = $plan_ver[0][0];
			$version = $plan_ver[0][1];
			$sql = "EXECUTE PROCEDURE sp_correlativasd('EXA', $carrera, '$legajo', '$materia', '$plan', '$version', TODAY, 'A')";
			$correlativasd = $db->query($sql)->fetchAll(PDO::FETCH_NUM); 
			
			$sql = "EXECUTE PROCEDURE sp_correlativa_esp('EXA', '$carrera', '$legajo', '$materia', '$plan', '$version', TODAY, 'A');";
			$correlativas_esp = $db->query($sql)->fetchAll(PDO::FETCH_NUM); 
			
			if ($correlativasd[0][0] == 1 && $correlativas_esp[0][0] == 1)
				$resultado[] = $alumnos[$i];
		}
		return $resultado;
	}
}
