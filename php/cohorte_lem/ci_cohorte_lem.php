<?php
require_once ('MisConsultas.php');

class ci_cohorte_lem extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$db = MisConsultas::getConexion ();
		$ingresantes_cohorte = $this->get_cant_ingresantes_cohorte($db);
		$activos_cohorte = $this->get_cant_activos_cohorte($db);
		$abandonaron_cohorte = $this->get_cant_abandonaron_cohorte($db);
		$egresaron_cohorte = $this->get_cant_egresaron_cohorte($db);
		$cant_falta_solo_tesis = $this->get_cant_falta_solo_tesis($db);
		
		$datos = $this->unir($ingresantes_cohorte, $activos_cohorte);
		$datos = $this->unir($datos, $abandonaron_cohorte);
		$datos = $this->unir($datos, $egresaron_cohorte);
		$datos = $this->unir($datos, $cant_falta_solo_tesis);
		//ei_arbol($datos, 'datos');
		$cuadro->set_datos($datos);

	}


	protected function unir($arr1, $arr2)
	{
		foreach ($arr2 as $k=>$arr)
		{
			foreach ($arr as $i=>$a)
			{
				$arr1[$k][$i] = $a;
			}
		}
		return $arr1;
	}

	protected function get_cant_ingresantes_cohorte($db)
	{
		$sql = "select PI.anio_academico, COUNT(*) as cant_ingresantes
					from sga_alumnos A
					join sga_carrera_aspira CA on (CA.carrera = A.carrera and CA.nro_inscripcion = A.nro_inscripcion)
					join sga_periodo_insc PI on (PI.periodo_inscripcio = CA.periodo_inscripcio)
					where A.carrera = 211
						and A.legajo NOT IN 
										(SELECT legajo FROM vw_hist_academica 
												WHERE vw_hist_academica.forma_aprobacion IN ('Equivalencia', 'Equivalencia equivalente')
												AND vw_hist_academica.resultado = 'A'
												AND vw_hist_academica.carrera = A.carrera
										) 
						and A.legajo NOT IN 
										(SELECT legajo FROM sga_cursadas 
												WHERE sga_cursadas.origen IN ('CE', 'E', 'EE')
												AND sga_cursadas.resultado = 'A'
												AND sga_cursadas.carrera = A.carrera)
				group by PI.anio_academico
				order by PI.anio_academico";

		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_NUM
		return $this->indexar($resultado, 'CANT_INGRESANTES');
	}

	protected function get_cant_abandonaron_cohorte($db)
	{
		$sql = "select PI.anio_academico, COUNT(*) as cant_abandonaron
					from sga_alumnos A
					join sga_carrera_aspira CA on (CA.carrera = A.carrera and CA.nro_inscripcion = A.nro_inscripcion)
					join sga_periodo_insc PI on (PI.periodo_inscripcio = CA.periodo_inscripcio)
					where A.carrera = 211
						and A.calidad = 'N'
						and A.legajo NOT IN 
										(SELECT legajo FROM vw_hist_academica 
												WHERE vw_hist_academica.forma_aprobacion IN ('Equivalencia', 'Equivalencia equivalente')
												AND vw_hist_academica.resultado = 'A'
												AND vw_hist_academica.carrera = A.carrera
										) 
						and A.legajo NOT IN 
										(SELECT legajo FROM sga_cursadas 
												WHERE sga_cursadas.origen IN ('CE', 'E', 'EE')
												AND sga_cursadas.resultado = 'A'
												AND sga_cursadas.carrera = A.carrera)
				group by PI.anio_academico
				order by PI.anio_academico";

		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_NUM
		return $this->indexar($resultado, 'CANT_ABANDONARON');
	}

	protected function get_cant_activos_cohorte($db)
	{
		$sql = "select PI.anio_academico, COUNT(*) as cant_activos
					from sga_alumnos A
					join sga_carrera_aspira CA on (CA.carrera = A.carrera and CA.nro_inscripcion = A.nro_inscripcion)
					join sga_periodo_insc PI on (PI.periodo_inscripcio = CA.periodo_inscripcio)
					where A.carrera = 211
						and A.calidad = 'A'
						and A.legajo NOT IN 
										(SELECT legajo FROM vw_hist_academica 
												WHERE vw_hist_academica.forma_aprobacion IN ('Equivalencia', 'Equivalencia equivalente')
												AND vw_hist_academica.resultado = 'A'
												AND vw_hist_academica.carrera = A.carrera
										) 
						and A.legajo NOT IN 
										(SELECT legajo FROM sga_cursadas 
												WHERE sga_cursadas.origen IN ('CE', 'E', 'EE')
												AND sga_cursadas.resultado = 'A'
												AND sga_cursadas.carrera = A.carrera)
				group by PI.anio_academico
				order by PI.anio_academico";

		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_NUM
		return $this->indexar($resultado, 'CANT_ACTIVOS');
	}
	
	protected function get_cant_egresaron_cohorte($db)
	{
		$sql = "select PI.anio_academico, COUNT(*) as cant_egresaron
					from sga_alumnos A
					join sga_carrera_aspira CA on (CA.carrera = A.carrera and CA.nro_inscripcion = A.nro_inscripcion)
					join sga_periodo_insc PI on (PI.periodo_inscripcio = CA.periodo_inscripcio)
					where A.carrera = 211
						and A.calidad = 'E'
						and A.legajo NOT IN 
										(SELECT legajo FROM vw_hist_academica 
												WHERE vw_hist_academica.forma_aprobacion IN ('Equivalencia', 'Equivalencia equivalente')
												AND vw_hist_academica.resultado = 'A'
												AND vw_hist_academica.carrera = A.carrera
										) 
						and A.legajo NOT IN 
										(SELECT legajo FROM sga_cursadas 
												WHERE sga_cursadas.origen IN ('CE', 'E', 'EE')
												AND sga_cursadas.resultado = 'A'
												AND sga_cursadas.carrera = A.carrera)
				group by PI.anio_academico
				order by PI.anio_academico";

		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_NUM
		return $this->indexar($resultado, 'CANT_EGRESARON');
	}

	protected function get_cant_falta_solo_tesis($db)
	{
		$sql = "select PI.anio_academico, COUNT(*) as cant_falta_solo_tesis
					from sga_alumnos A
					join sga_carrera_aspira CA on (CA.carrera = A.carrera and CA.nro_inscripcion = A.nro_inscripcion)
					join sga_periodo_insc PI on (PI.periodo_inscripcio = CA.periodo_inscripcio)
					where A.carrera = 211
						and A.legajo NOT IN 
										(SELECT legajo FROM vw_hist_academica 
												WHERE vw_hist_academica.forma_aprobacion IN ('Equivalencia', 'Equivalencia equivalente')
												AND vw_hist_academica.resultado = 'A'
												AND vw_hist_academica.carrera = A.carrera
										) 
						and A.legajo NOT IN 
										(SELECT legajo FROM sga_cursadas 
												WHERE sga_cursadas.origen IN ('CE', 'E', 'EE')
												AND sga_cursadas.resultado = 'A'
												AND sga_cursadas.carrera = A.carrera)
						and A.legajo NOT IN 
										(SELECT legajo FROM vw_hist_academica 
												WHERE vw_hist_academica.materia = '0207' --Tesis
												AND vw_hist_academica.resultado = 'A'
												AND vw_hist_academica.carrera = A.carrera
										) 
						and A.legajo IN 
										(SELECT legajo FROM vw_hist_academica 
												WHERE vw_hist_academica.resultado = 'A'
												AND vw_hist_academica.carrera = A.carrera
												and vw_hist_academica.materia NOT IN ('0017', '0207')  --Inglés y Tesis
												group by legajo 
												having count(legajo) = 8
										) 

				group by PI.anio_academico
				order by PI.anio_academico";

		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_NUM
		return $this->indexar($resultado, 'CANT_FALTA_SOLO_TESIS');
	}

	protected function indexar($arreglo, $index)
	{
		$resultado = array();
		foreach($arreglo as $a)
		{
			$resultado[$a['ANIO_ACADEMICO']] = array($index => $a[$index]);
			$resultado[$a['ANIO_ACADEMICO']]['ANIO'] = $a['ANIO_ACADEMICO'];
		}
		return $resultado;
	}
}

?>