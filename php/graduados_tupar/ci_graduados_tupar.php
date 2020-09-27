<?php
require_once 'MisConsultas.php';

class ci_graduados_tupar extends toba_ci
{
	function get_egresados()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT P.apellido || ', ' || P.nombres AS egresado, V.e_mail, YEAR(fecha_egreso) AS anio_egreso
						FROM sga_titulos_otorg T, sga_personas P, vw_datos_censales_actuales V
						WHERE T.carrera = 212
							AND T.nro_inscripcion = P.nro_inscripcion
							AND P.nro_inscripcion = V.nro_inscripcion
					ORDER BY 3, 1";
		$egresados = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $egresados;
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = self::get_egresados();
		$cuadro->set_datos($datos);
	}

}

?>