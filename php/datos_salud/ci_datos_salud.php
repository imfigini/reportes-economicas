<?php
require_once 'MisConsultas.php';

class ci_datos_salud extends toba_ci
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
			$datos = $this->get_datos_salud($filtro);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}

	//-----------------------------------------------------------------------------

	static function get_conexion($base)
	{
		switch($base)
		{
			case 'siu_guarani_ingr': 
				return MisConsultas::getConexionMini();
			case 'siu_guarani': 
				return MisConsultas::getConexion();
		}
		return null;
	}

	function get_datos_salud($filtro)
	{
//		ei_arbol($filtro);
		$base = $filtro['base'];
		$db = self::get_conexion($base);

		$sql = "SELECT DISTINCT 
					S.nombre AS sede,
					P.nro_inscripcion, 
					P.nro_documento, 
					P.apellido || ', ' || P.nombres AS alumno, 
					C.nombre_reducido AS carrera,
					DT.descripcion AS tipo_discapacidad, 
					DC.descripcion AS tipo_caracter,
					DG.descripcion AS tipo_grado,
					DAT.fecha_desde,
					DAT.fecha_hasta, 
					DECODE(DAT.certificado, 'S', 'Si', 'N', 'No', '') AS certificado,
					DECODE(DAT.cobertura_medica, 'S', 'Si', 'N', 'No', '') AS cobertura_medica,
					DAT.observaciones
				FROM sga_datos_salud DAT
				JOIN sga_personas P ON (P.unidad_academica = DAT.unidad_academica AND P.nro_inscripcion = DAT.nro_inscripcion)
				JOIN sga_alumnos A ON (A.unidad_academica = DAT.unidad_academica AND A.nro_inscripcion = DAT.nro_inscripcion)
				JOIN sga_sedes S ON (S.sede = A.sede)
				JOIN sga_carreras C ON (C.carrera = A.carrera)
				JOIN mdp_discap_tipos DT ON (DT.tipo_discapacidad = DAT.tipo_discapacidad)
				JOIN mdp_discap_caracter DC ON (DC.tipo_caracter = DAT.tipo_caracter)
				JOIN mdp_discap_grado DG ON (DG.tipo_grado = DAT.tipo_grado)
					WHERE A.regular = 'S' 
					AND A.calidad = 'A' ";
		if (isset($filtro['anio_ingreso']))
		{
			$anio_ingreso = $filtro['anio_ingreso'];
			$sql .= " AND A.nro_inscripcion IN (SELECT nro_inscripcion 
													FROM sga_carrera_aspira CA
													JOIN sga_periodo_insc PI ON (PI.periodo_inscripcio = CA.periodo_inscripcio)
													WHERE PI.anio_academico = $anio_ingreso
													AND CA.carrera = A.carrera) ";
		}
		if (isset($filtro['carrera']))
		{
			$carrera = $filtro['carrera'];
			$sql .= " AND A.carrera = $carrera ";
		}
		if (isset($filtro['descripcion']))
		{
			$descripcion = $filtro['descripcion'];
			$sql .= " AND (	P.nro_documento LIKE '%$descripcion%'
							OR lower(P.apellido) LIKE lower('%$descripcion%')
							OR lower(P.nombres) LIKE lower('%$descripcion%')
						   ) ";
		}
		return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	static function get_carreras($base) 
    {
		$db = self::get_conexion($base);
        $sqlText = "SELECT carrera, nombre || ' (' || carrera || ')' AS nombre 
                        FROM sga_carreras 
                        WHERE estado = 'A'
                                AND carrera <> 290
                        ORDER BY nombre;";

        return $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
	}	

	
	static function get_anios($base)
	{
		$db = self::get_conexion($base);
		$sql = 'SELECT anio_academico FROM sga_anio_academico ORDER BY 1 DESC';
		return $db->query($sql);
	}
	

}

?>