<?php
require_once("MisConsultas.php");

class ci_egresados extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = self::get_egresados();
		$cuadro->set_datos($datos);
	}

	function get_egresados()
	{
		$sql = "SELECT 	P.apellido || ', ' || P.nombres as alumno,
						A.fecha_ingreso,
						T1.fecha_egreso,
						C.nombre as carrera,
						T.nombre as titulo,
						T.nivel
				FROM sga_titulos_otorg T1
				join sga_personas P on (P.nro_inscripcion = T1.nro_inscripcion)
				join sga_alumnos A on (A.nro_inscripcion = T1.nro_inscripcion and A.carrera = T1.carrera)
				join sga_carreras C on (C.carrera = T1.carrera)
				join sga_titulos T on (T.titulo = T1.titulo)
				where fecha_egreso in (select max(fecha_egreso) from sga_titulos_otorg T2
											where nro_inscripcion = T1.nro_inscripcion
											and carrera = T1.carrera)
				order by 1 ";
		$db = MisConsultas::getConexion ();
		return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}
}

?>