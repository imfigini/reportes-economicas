<?php
require_once('MisConsultas.php');

class ci_cursando_mas_de_una_vez extends toba_ci
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
			$carrera = $filtro['CARRERA'];
			$materia = $filtro['MATERIA'];
			$datos = self::get_cursando_mas_una_vez($carrera, $materia);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}

	//-------------------------------------------------------------------------------------


	static function get_inscriptos_materia($carrera, $materia)
	{
		$sql = "SELECT 	IC.carrera, 
						IC.legajo, 
						P.apellido || ', ' || P.nombres AS alumno, 
						NVL(G.e_mail, '') AS e_mail,
						S.nombre AS sede,
						IC.comision, 
						COM.materia
					FROM sga_insc_cursadas IC
					JOIN sga_comisiones COM ON (COM.unidad_academica = IC.unidad_academica AND COM.comision = IC.comision)
					JOIN sga_periodos_lect PL ON (PL.anio_academico = COM.anio_academico AND PL.periodo_lectivo = COM.periodo_lectivo)
					JOIN sga_alumnos A ON (A.unidad_academica = IC.unidad_academica AND A.carrera = IC.carrera AND A.legajo = IC.legajo)
					JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
					JOIN sga_sedes S ON (S.sede = COM.sede)
					LEFT JOIN gda_anun_conf_pers G ON (G.unidad_academica = P.unidad_academica AND G.nro_inscripcion = P.nro_inscripcion)
						WHERE COM.materia = '$materia'
						AND IC.carrera = '$carrera'
						AND TODAY BETWEEN PL.fecha_inicio AND PL.fecha_fin ";

		$db = MisConsultas::getConexion();
		return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);				
	}

	static function get_cursando_mas_una_vez($carrera, $materia)
	{
		$db = MisConsultas::getConexion();
		
		$inscriptos = self::get_inscriptos_materia($carrera, $materia);

		$resultado = Array();
		foreach ($inscriptos AS $inscripto)
		{
			$legajo = $inscripto['LEGAJO'];
			//Cuento la cantidad de veces que la cursó en cursadas anteriores (descarto la cursada del cuatrimestre actual)
			$sql = "SELECT COUNT(*) AS cant
					FROM sga_cursadas CUR
					JOIN sga_alumnos A ON (A.unidad_academica = CUR.unidad_academica AND A.carrera = CUR.carrera AND A.legajo = CUR.legajo)
						WHERE A.regular = 'S' AND A.calidad = 'A'
						AND A.carrera = '$carrera'
						AND A.legajo = '$legajo'
						AND CUR.materia = '$materia'
						AND CUR.comision NOT IN (SELECT comision 
										FROM sga_comisiones COM
										JOIN sga_periodos_lect PL ON (PL.anio_academico = COM.anio_academico AND PL.periodo_lectivo = COM.periodo_lectivo)
											WHERE COM.materia = '$materia'
											AND TODAY BETWEEN PL.fecha_inicio AND PL.fecha_fin)	";

			$cant = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			if ($cant[0]['CANT'] == 0)	{
				continue;
			}
			$inscripto['CANT'] = $cant[0]['CANT'] + 1;
			$resultado[] = $inscripto;
		}
		return $resultado;
		
	}


}

?>