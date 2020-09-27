<?php
require_once('MisConsultas.php');

class ci_habilitados_cursar_materia extends toba_ci
{
	protected $s__datos_filtro;


	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio_ingreso = $filtro['ANIO_INGRESO'];
			$carrera = $filtro['CARRERA'];
			$materia = $filtro['MATERIA'];
			$datos = self::get_habilitados_cursar($anio_ingreso, $carrera, $materia);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}

	//---------------------------------------------------------------------------------------

	static function get_habilitados_cursar($anio_ingreso, $carrera, $materia)
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT 	S.nombre AS sede,
						A.legajo, 
						P.apellido || ', ' || P.nombres AS alumno,
						C.carrera, 
						C.nombre_reducido AS carrera_nombre,
						NVL(G.e_mail, '') AS e_mail
					FROM sga_alumnos A
					JOIN sga_carrera_aspira ASP ON (ASP.unidad_academica = A.unidad_academica AND ASP.carrera = A.carrera AND ASP.nro_inscripcion = A.nro_inscripcion)
					JOIN sga_periodo_insc PI ON (PI.periodo_inscripcio = ASP.periodo_inscripcio AND PI.anio_academico = $anio_ingreso)
					JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
					JOIN sga_sedes S ON (S.sede = A.sede)
					JOIN sga_carreras C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera)
					LEFT JOIN gda_anun_conf_pers G ON (G.unidad_academica = A.unidad_academica AND G.nro_inscripcion = A.nro_inscripcion)
					WHERE A.regular = 'S'
					AND A.calidad = 'A'
					AND A.carrera = $carrera
					AND A.legajo NOT IN (SELECT legajo FROM vw_hist_academica
								WHERE carrera = A.carrera
								AND materia = '$materia'
								AND resultado = 'A')
					AND A.legajo NOT IN (SELECT legajo FROM sga_cursadas
								WHERE carrera = A.carrera
								AND materia = '$materia'
								AND resultado IN ('A', 'P')) ";
			$posibles =  $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			//ei_arbol($posibles);

			$resultado = Array();
			foreach ($posibles AS $posible)
			{
				$ua = 'EXA';
				$legajo = $posible['LEGAJO'];
				$sql = "EXECUTE PROCEDURE ctr_corrinsccurs('$ua', '$carrera', '$legajo', '$materia') ";
				$dato = $db->query($sql)->fetchAll();
				if ($dato[0][0] != 1) {
					continue;
				}
				$sql = "EXECUTE PROCEDURE ctr_cursada_NM4('$ua', '$carrera', '$legajo', '$materia') ";
				$dato = $db->query($sql)->fetchAll();
				if ($dato[0][0] != 1) {
					continue;
				}
				$sql = "EXECUTE PROCEDURE ctr_cursada_NM5('$ua', '$carrera', '$legajo', '$materia') ";
				$dato = $db->query($sql)->fetchAll();
				if ($dato[0][0] != 1) {
					continue;
				}
				$resultado[] = $posible;
			}
			//ei_arbol($resultado);
			return $resultado;
	}
}

?>