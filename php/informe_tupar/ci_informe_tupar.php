<?php
require_once 'MisConsultas.php';
define("CARRERA", 212);

class ci_informe_tupar extends toba_ci
{
	
	function get_ingresantes_totales($db)
	{
		$carrera = CARRERA;
		$sql = "SELECT YEAR(fecha_ingreso) as anio, COUNT(*) as cantidad
								FROM sga_alumnos 
								WHERE carrera = $carrera
						GROUP BY 1
						ORDER BY 1";
		$ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $ingresantes;
	}
	
	function get_ingresantes_cohorte($db)
	{
		$carrera = CARRERA;
		$sql = "SELECT YEAR(fecha_ingreso) as anio, COUNT(*) as cantidad FROM sga_alumnos WHERE carrera = $carrera
						AND legajo NOT IN (
							SELECT legajo FROM vw_hist_academica WHERE carrera = $carrera AND forma_aprobacion LIKE 'Equi%'
						)
					GROUP BY 1
					ORDER BY 1";
		$cohorte = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $cohorte;
	}
	
	function get_ingresantes_equivalencia($db)
	{
		$carrera = CARRERA;
		$sql = "SELECT YEAR(fecha_ingreso) as anio, COUNT(*) as cantidad FROM sga_alumnos WHERE carrera = $carrera
						AND legajo IN (
							SELECT legajo FROM vw_hist_academica WHERE carrera = $carrera AND forma_aprobacion LIKE 'Equi%'
						)
					GROUP BY 1
					ORDER BY 1";
		$equiv = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $equiv;
	}

	function get_reinscriptos($db)
	{
		$carrera = CARRERA;
		$sql = "SELECT anio_academico as anio, COUNT(*) as cantidad
						FROM sga_reinscripcion 
						WHERE carrera = $carrera
					GROUP BY anio_academico
					ORDER BY anio_academico;";
		$reinscriptos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $reinscriptos;
	}

	function calcular_total_alumnos($ingresantes, $reinscriptos)
	{
		$resultado = self::inicializar_arreglo_con_anios();
		$max = count($resultado);
		for ($i=0; $i<$max; $i++)
		{
			$resultado[$i]['CANTIDAD'] = 0;
			foreach ($ingresantes as $ingr)
			{
				if ($resultado[$i]['ANIO'] == $ingr['ANIO'])
					$resultado[$i]['CANTIDAD'] += $ingr['CANTIDAD'];
			}
			foreach ($reinscriptos as $reinsc)
			{
				if ($resultado[$i]['ANIO'] == $reinsc['ANIO'])
					$resultado[$i]['CANTIDAD'] += $reinsc['CANTIDAD'];
			}
		}
		return $resultado;
	}
	
	function get_egresados($db)
	{
		$carrera= CARRERA;
		$sql = "SELECT YEAR(fecha_egreso) as anio, COUNT(*) as cantidad
						FROM sga_titulos_otorg 
						WHERE carrera = $carrera
					GROUP BY 1
					ORDER BY 1";
		$egresados = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $egresados;
	}	
	function inicializar_arreglo_con_anios()
	{
		$resultado = Array();
		$anio_actual = getdate();
		$anio_actual = $anio_actual['year'];
		for ($i=2007; $i<=$anio_actual; $i++)
			$resultado[]['ANIO'] = $i;
		return $resultado;
	}
	
	function agregar_resultados($resultado, $arreglo, $etiqueta)
	{
		$max = count($resultado);
		for ($i=0; $i<$max; $i++)
		{
			$existe = 0;
			foreach ($arreglo as $arr)
			{
				if ($resultado[$i]['ANIO'] == $arr['ANIO'])
				{
					$resultado[$i]["$etiqueta"] = $arr['CANTIDAD'];
					$existe = 1;
				}
			}
			if (!$existe)
				$resultado[$i]["$etiqueta"] = 0;
		}
		return $resultado;
	}
	

	function get_datos_tupar()
	{
		$db = MisConsultas::getConexion();
		$resultado = self::inicializar_arreglo_con_anios();

		$ingresantes = self::get_ingresantes_totales($db);
		$resultado = self::agregar_resultados($resultado, $ingresantes, 'INGRESANTES');

		$ingresantes_cohorte = self::get_ingresantes_cohorte($db);
		$resultado = self::agregar_resultados($resultado, $ingresantes_cohorte, 'INGR_COHORTE');

		$ingresantes_equivalencia = self::get_ingresantes_equivalencia($db);
		$resultado = self::agregar_resultados($resultado, $ingresantes_equivalencia, 'INGR_EQUIVALENCIA');

		$reinscriptos = self::get_reinscriptos($db);
		$resultado = self::agregar_resultados($resultado, $reinscriptos, 'REINSCRIPTOS');

		$total_alumnos = self::calcular_total_alumnos($ingresantes, $reinscriptos);
		$resultado = self::agregar_resultados($resultado, $total_alumnos, 'TOTAL_ALUMNOS');

		$egresados = self::get_egresados($db);
		$resultado = self::agregar_resultados($resultado, $egresados, 'EGRESADOS');

		return $resultado;
	}
	
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = self::get_datos_tupar();
		$cuadro->set_datos($datos);
	}

}

?>