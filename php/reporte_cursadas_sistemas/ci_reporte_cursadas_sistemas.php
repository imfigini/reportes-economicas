<?php
require_once('MisConsultas.php');

class ci_reporte_cursadas_sistemas extends toba_ci
{
	function get_cursadas_sistemas()
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
						fecha_regularidad AS fecha_regularidad,
						CASE 	WHEN origen = 'P' THEN 'Promocion'
								WHEN origen = 'C' THEN 'Cursada'
								WHEN origen IN ('E', 'EE', 'CE') THEN 'Equivalencia'
								ELSE ''
							END AS origen
					FROM 	rep_sistemas_cursadas
						WHERE departamento = 'D003'";		/*Sólo muestra las materias de Sistemas*/
		
		$db = MisConsultas::getConexion ();
		$cursadas = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $cursadas;
	}
	
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_cursadas_sistemas();
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
						WHERE tabla = 'rep_sistemas_cursadas'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}

	//-----------------------------------------------------------------------------------
	//---- tudai_cursadas_procesar ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__sistemas_cursadas_procesar__procesar($datos)
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE 'dba'.sp_rep_sistemas_cursadas()";
		$result = $db->query($sql)->fetchAll(); //->fetchAll();
	}

}
?>