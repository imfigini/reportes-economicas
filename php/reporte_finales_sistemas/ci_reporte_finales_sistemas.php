<?php
require_once('MisConsultas.php');

class ci_reporte_finales_sistemas extends toba_ci
{
	
	function get_finales_sistemas()
	{
		$sql = "SELECT 	legajo, 
						plan,
						YEAR(fecha_ingreso) AS anio_ingreso,
						NVL(nombre_materia, '') AS materia,
						NVL(anio_de_cursada, '') AS anio_de_cursada,
						CASE	WHEN resultado = 'P' THEN 'Promovido'
								WHEN resultado = 'A' THEN 'Aprobado'
								WHEN resultado = 'R' THEN 'Reprobado'
								WHEN resultado = 'U' THEN 'Ausente'
								ELSE ''
							END AS resultado, 
						NVL(fecha, '') AS fecha,
						NVL(forma_aprobacion, '') AS forma_aprobacion
					FROM 	rep_sistemas_finales
						WHERE departamento = 'D003'";		/*Sólo muestra las materias de Sistemas*/
		
		$db = MisConsultas::getConexion ();
		$finales = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $finales;
	}


	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_finales_sistemas();
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- Formulario ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function get_ultima_actualizacion()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
					FROM rep_fecha_actualiz_tablas
						WHERE tabla = 'rep_sistemas_finales'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}

	//-----------------------------------------------------------------------------------
	//---- tudai_cursadas_procesar ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__sistemas_finales_procesar__procesar($datos)
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE 'dba'.sp_rep_sistemas_finales()";
		$result = $db->query($sql)->fetchAll(); //->fetchAll();
	}

}
?>