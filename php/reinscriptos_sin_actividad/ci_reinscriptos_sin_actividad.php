<?php
require_once('MisConsultas.php');

class ci_reinscriptos_sin_actividad extends toba_ci
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
		if (isset($this->s__datos_filtro)) {
			$resultado = $this->get_reinscriptos_sin_actividad($this->s__datos_filtro);
			$cuadro->set_datos($resultado);
		} else {
			$cuadro->limpiar_columnas();
		}
	}

	//---- Funciones ---------------------------------------------------------------------
	/**
    * -- Listado de alumnos cuyo último año de reinscripción es el pasado por parámetro
    */
    function get_reinscriptos_sin_actividad($filtro)
    {
		$anio_reinscripcion = $filtro['anio_academico'];

		$sql = "SELECT	P.apellido || ', ' || P.nombres AS alumno, 
						P.nro_documento, 
						YEAR(A.fecha_ingreso) AS fecha_ingreso,
						A.carrera,
						A.legajo, 
						V.te_per_lect AS telefono, 
						V.e_mail
					FROM  sga_alumnos A
					JOIN sga_reinscripcion R ON (A.unidad_academica = R.unidad_academica AND A.carrera = R.carrera AND A.legajo = R.legajo)
					JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
					LEFT JOIN vw_datos_censales_actuales V ON (V.unidad_academica = P.unidad_academica AND V.nro_inscripcion = P.nro_inscripcion)
						WHERE  A.calidad = 'A'
						GROUP BY 1,2,3,4,5,6,7
				HAVING MAX (R.anio_academico) = $anio_reinscripcion
				ORDER BY alumno";

		$db = MisConsultas::getConexion();
		$reinscriptos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		$resultado = array();
		foreach ($reinscriptos as $alumno)
		{
				$legajo = $alumno['LEGAJO'];
				$carrera = $alumno['CARRERA'];
				$alumno = MisConsultas::agregaAnioUltimaActividad($db, $alumno, $legajo, $carrera);
				$alumno['PORCENTAJE_AVANCE'] = $this->agregaPorcentajeAvance($db, $legajo, $carrera);
				$resultado[] = $alumno;
		}
		//ei_arbol($resultado);
		$resultado = MisConsultas::addFakeId($resultado);
		return $resultado;
	}	
	
	static public function agregaPorcentajeAvance($db, $legajo, $carrera)
    {
        $ua = UNIDAD_ACAD;
		$sql = "EXECUTE PROCEDURE 'dba'.sp808_porc_der('$ua', '$carrera', '$legajo')";
        $porc_avance = $db->query($sql)->fetchAll();
        return $porc_avance[0][0]; 
    }		
}