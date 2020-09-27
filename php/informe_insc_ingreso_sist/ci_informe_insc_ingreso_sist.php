<?php
require_once('MisConsultas.php');
//require_once('informe_inscriptos/consultas_inscriptos.php');
// Reporte basado en php/informe_inscriptos

class ci_informe_insc_ingreso_sist extends toba_ci
{
	protected $s__datos_filtro;
	protected $s__carreras = " ('206', '213', '212') ";  //Ing. Sistemas, TUDAI, TUPAR
	
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
			$anio = $filtro['anio_academico'];
			$inscr = $this->get_cant_inscriptos_carrera($anio);
			$cuadro->set_datos($inscr);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	function conf__cuadro_instancias(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$datos = $this->get_inscripciones_x_instancia($anio);
			$cuadro->set_datos($datos);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	//---- Formulario ------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];

			$db = MisConsultas::getConexionMini ($anio);

			$nuevos = $this->get_cant_nuevos_inscriptos($db, $anio);
			$reinscriptos = $this->get_cant_reinscriptos($db, $anio);

			$resultado['TOTAL_INSCRIPTOS'] = $nuevos + $reinscriptos;
			return $resultado;
		}
		else {
			$form->set_datos(null);
		}
	}

	//---- Funciones ------------------------------------------------------------------

	/*
	 * Obtiene la cantidad total de nuevos inscriptos al curso de ingreso en un determinado año (sin repetir el legajo)
	*/
	private function get_cant_nuevos_inscriptos($db, $anio)
	{
		$sql = "SELECT COUNT(DISTINCT A.nro_inscripcion) AS total
		FROM sga_alumnos A, sga_carrera_aspira S, sga_periodo_insc P
			WHERE A.unidad_academica = S.unidad_academica
			AND A.nro_inscripcion = S.nro_inscripcion
			AND A.regular = 'S'
			AND A.calidad = 'A'
			AND S.periodo_inscripcio = P.periodo_inscripcio
			AND P.anio_academico = '$anio'
			AND A.carrera IN $this->s__carreras ";

		$nuevos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $nuevos[0]['TOTAL'];
	}

	/*
	 * Obtiene la cantidad total de reinscriptos al curso de ingreso en un determinado año (sin repetir legajo)
	*/
	private function get_cant_reinscriptos($db, $anio)
	{
		$sql = "SELECT COUNT(DISTINCT A.nro_inscripcion) AS total
		FROM sga_alumnos A, sga_reinscripcion R
			WHERE A.unidad_academica = R.unidad_academica
			AND A.legajo = R.legajo
			AND A.carrera = R.carrera
			AND A.regular = 'S'
			AND A.calidad = 'A'
			AND R.anio_academico = '$anio'
			AND A.carrera IN $this->s__carreras
			AND A.legajo NOT IN (SELECT AA.legajo
									FROM sga_alumnos AA, sga_carrera_aspira SS, sga_periodo_insc PP
										WHERE AA.unidad_academica = SS.unidad_academica
										AND AA.nro_inscripcion = SS.nro_inscripcion
										AND AA.regular = 'S'
										AND AA.calidad = 'A'
										AND SS.periodo_inscripcio = PP.periodo_inscripcio
										AND PP.anio_academico = '$anio'
										AND AA.carrera IN $this->s__carreras)";

		$reinscriptos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $reinscriptos[0]['TOTAL'];
	}

	/*
	 * Obtiene la cantidad de inscriptos por carrera al curso de ingreso en un determinado año (nuevos + reinscriptos)
	*/
	private function get_cant_inscriptos_carrera($anio)
	{
		$db = MisConsultas::getConexionMini ($anio);
		
		$nuevos_carrea = $this->get_cant_nuevos_inscriptos_carrera($db, $anio);
		$reinscriptos_carrera = $this->get_cant_reinscriptos_carrera($db, $anio);

		$resultado = Array();
		$cant_nuevos = count($nuevos_carrea);
		for ($i=0; $i<$cant_nuevos; $i++)
		{
			$result['CARRERA'] = $nuevos_carrea[$i]['CARRERA'];
			$result['CARRERA_NOMBRE'] = $nuevos_carrea[$i]['CARRERA_NOMBRE'];
			$result['CANT_INSCRIPTOS'] = $nuevos_carrea[$i]['TOTAL'];
			foreach ($reinscriptos_carrera AS $reinscriptos) 
			{
				if ($reinscriptos['CARRERA'] == $result['CARRERA'])
				{
					$result['CANT_INSCRIPTOS'] += $reinscriptos['TOTAL'];
				}
			}
			$resultado[] = $result;
		}
		return $resultado;
	}

	/*
	 * Obtiene la cantidad de nuevos inscriptos por carrera al curso de ingreso en un determinado año
	*/
	private function get_cant_nuevos_inscriptos_carrera($db, $anio)
	{
		$sql = "SELECT COUNT(DISTINCT A.nro_inscripcion) AS total, C.carrera, C.nombre AS carrera_nombre
		FROM sga_alumnos A, sga_carrera_aspira S, sga_periodo_insc P, sga_carreras C
			WHERE A.unidad_academica = S.unidad_academica
			AND A.nro_inscripcion = S.nro_inscripcion
			AND A.regular = 'S'
			AND A.calidad = 'A'
			AND S.periodo_inscripcio = P.periodo_inscripcio
			AND A.carrera = C.carrera
			AND P.anio_academico = '$anio'
			AND A.carrera IN $this->s__carreras
		GROUP BY C.carrera, C.nombre ";

		$nuevos_carrera = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $nuevos_carrera;
	}

	/*
	 * Obtiene la cantidad de reinscriptos por carrera al curso de ingreso en un determinado año
	*/
	private function get_cant_reinscriptos_carrera($db, $anio)
	{
		$sql = "SELECT COUNT(DISTINCT A.nro_inscripcion) AS total, C.carrera, C.nombre AS carrera_nombre
		FROM sga_alumnos A, sga_reinscripcion R, sga_carreras C
			WHERE A.unidad_academica = R.unidad_academica
			AND A.legajo = R.legajo
			AND A.carrera = R.carrera
			AND A.regular = 'S'
			AND A.calidad = 'A'
			AND A.carrera = C.carrera
			AND R.anio_academico = '$anio'
			AND A.carrera IN $this->s__carreras
		GROUP BY C.carrera, C.nombre ";
		$reinscriptos_carrera = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $reinscriptos_carrera;
	}

	//---- Funciones para inscriots / aprobados por instancia -------------------------------------------------------------

	private function get_inscripciones_x_instancia($anio)
	{
		$db = MisConsultas::getConexionMini();
		$datos = array();
		
		$cant_curs = $this->get_inscripciones_cursada($db, $anio);
		$cant_exam = $this->get_inscripciones_examen($db, $anio); 
		$total = array_merge($cant_curs, $cant_exam);
		//ei_arbol($total, 'total');
		return $total;
	}
        
	private function get_inscripciones_cursada ($db, $anio)
	{
		$sql = "SELECT 	R.carrera, 
						R.nombre AS carrera_nombre, 
						M.materia, 
						M.nombre AS materia_nombre, 
						PL.periodo_lectivo AS instancia, 
						COUNT(*) AS cant_inscriptos
					FROM sga_periodos_lect PL
					JOIN sga_comisiones C ON (C.anio_academico = PL.anio_academico AND C.periodo_lectivo = PL.periodo_lectivo)
					JOIN sga_insc_cursadas IC ON (IC.comision = C.comision)
					JOIN sga_carreras R ON (R.carrera = IC.carrera)
					JOIN sga_alumnos A ON (A.carrera = IC.carrera AND A.legajo = IC.legajo AND A.regular = 'S' AND A.calidad = 'A')
					JOIN sga_materias M ON (C.materia = M.materia)
				WHERE PL.anio_academico = $anio
					AND IC.carrera IN $this->s__carreras
				GROUP BY 1, 2, 3, 4, 5
				ORDER BY 2, 4 ";
		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		$cant = count($resultado);
		for ($i=0; $i<$cant; $i++) 
		{
			$carrera = $resultado[$i]['CARRERA'];
			$materia = $resultado[$i]['MATERIA'];
			$periodo = $resultado[$i]['INSTANCIA'];
			$resultado[$i]['CANT_APROBADOS'] = $this->get_cant_aprobados_cursada($db, $anio, $carrera, $materia, $periodo);
		}
		return $resultado;
	}
	
	private function get_cant_aprobados_cursada($db, $anio, $carrera, $materia, $periodo) 
	{
		$sql = "SELECT COUNT(legajo) AS cant_aprobados
					FROM sga_actas_cursado AC, sga_det_acta_curs D, sga_comisiones C
					WHERE AC.acta = D.acta
						AND AC.comision = C.comision
						AND C.materia = '$materia'
						AND C.anio_academico = $anio
						AND C.periodo_lectivo = '$periodo'
						AND resultado IN ('A', 'P')
						AND D.carrera = $carrera ";
		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);	
		return $resultado[0]['CANT_APROBADOS'];		
	}

	private function get_inscripciones_examen ($db, $anio)
	{
		$sql = "SELECT 	R.carrera, 
						R.nombre AS carrera_nombre, 
						M.materia, 
						M.nombre AS materia_nombre, 
						TE.turno_examen, 
						TE.nombre AS instancia, 
						COUNT(*) AS cant_inscriptos
					FROM sga_turnos_examen TE
					JOIN sga_insc_examen IE ON (IE.anio_academico = TE.anio_academico AND IE.turno_examen = TE.turno_examen)
					JOIN sga_carreras R ON (R.carrera = IE.carrera)
					JOIN sga_alumnos A ON (A.carrera = IE.carrera AND A.legajo = IE.legajo AND A.regular = 'S' AND A.calidad = 'A')
					JOIN sga_materias M ON (IE.materia = M.materia)
				WHERE TE.anio_academico = $anio
					AND IE.carrera IN $this->s__carreras
				GROUP BY 1, 2, 3, 4, 5, 6
				ORDER BY 2, 4 ";
		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$cant = count($resultado);
		for ($i=0; $i<$cant; $i++) 
		{
			$carrera = $resultado[$i]['CARRERA'];
			$materia = $resultado[$i]['MATERIA'];
			$turno_examen = $resultado[$i]['TURNO_EXAMEN'];
			$resultado[$i]['CANT_APROBADOS'] = $this->get_cant_aprobados_final($db, $anio, $carrera, $materia, $turno_examen);
		}
		return $resultado;
	}

	private function get_cant_aprobados_final($db, $anio, $carrera, $materia, $turno_examen) 
	{
		$sql = "SELECT COUNT(legajo) AS cant_aprobados
					FROM sga_actas_examen AE, sga_detalle_acta D
					WHERE AE.acta = D.acta
					AND AE.materia = '$materia'
					AND AE.anio_academico = $anio
					AND AE.turno_examen = $turno_examen
					AND D.resultado IN ('A')
					AND D.carrera = '$carrera' ";
		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);	
		return $resultado[0]['CANT_APROBADOS'];		
	}
	
}

?>