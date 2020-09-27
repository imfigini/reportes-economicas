<?php
require_once("MisConsultas.php");

class ci_materias_alumno_moodle extends toba_ci
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

	function get_anios()
	{
		$sql = "SELECT anio_academico
				FROM   dba.sga_anio_academico
				ORDER  BY anio_academico DESC";
		$db = MisConsultas::getConexion ();
		return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
            {
                $datos = self::getMateriasInscriptoAlumno($this->s__datos_filtro);
                $cuadro->set_datos($datos);
            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
	}

	function getMateriasInscriptoAlumno($filtro)
	{
		$alumno = $filtro['alumno'];
		$sql = "SELECT 	P.nro_documento, 
						P.apellido || ', ' || P.nombres as alumno, 
						M.materia, 
						M.nombre AS materia_nombre,
						C.anio_academico,
						C.periodo_lectivo,
						S.nombre AS sede
				FROM   	dba.sga_insc_cursadas I,
						sga_alumnos A,
						sga_personas P,
						sga_comisiones C,
						sga_materias M,
						sga_sedes S
				WHERE  I.legajo = A.legajo
						AND I.carrera = A.carrera
						AND A.nro_inscripcion = P.nro_inscripcion
						AND I.comision = C.comision
						AND C.materia = M.materia
						AND S.sede = C.sede
						AND ( P.nro_documento LIKE '%$alumno%'
							OR lower(apellido) LIKE lower('%$alumno%')
							OR lower(nombres) LIKE lower('%$alumno%') ) ";

		if (isset ($filtro['anio'])) {
			$anio = $filtro['anio'];
			$sql .= " AND C.anio_academico = $anio ";
		}

		$sql .= " ORDER BY 	P.apellido ASC,
							P.nombres ASC,
							C.anio_academico DESC,
							C.periodo_lectivo ASC,
							M.nombre ASC";

		$db = MisConsultas::getConexion ();
		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

}

?>