<?php
require_once('MisConsultas.php');

class ci_estado_tesis extends toba_ci
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
			$estado = $this->s__datos_filtro['ESTADO'];
		} 
		else 
		{
			$estado = null;
		}
		$datos = $this->get_tesis($estado);
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
	}
	
	//------ Funcionalidad --------------------------------------------------------------

	function get_estados()
	{
        $db = MisConsultas::getConexion();
        $sql = "select estado, nombre 
					from sga_tesis_estados
					ORDER BY 2";

        $estados = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $estados;        
	}

	function get_tesis($estado)
	{
		$db = MisConsultas::getConexion();
		$sql = "select 	A.legajo,
						A.carrera,
						P.apellido || ', ' || P.nombres as alumno,
						C.nombre as carrera_nombre,
						T.titulo as titulo_tesis, 
						T.fecha_alta,
						T.fecha_aprobacion,
						E.nombre as estado_nombre
				from sga_tesis T
				join sga_tesis_estados E on (E.estado = T.estado)
				join sga_tesis_alumnos TA on (TA.tesis = T.tesis)
				join sga_alumnos A on (A.legajo = TA.legajo and A.carrera = TA.carrera)
				join sga_carreras C on (A.carrera = C.carrera)
				join sga_personas P on (p.nro_inscripcion = A.nro_inscripcion) ";

		if (isset($estado))
		{
			$sql .= " where T.estado = '$estado' ";
		}
		$sql .= " order by fecha_alta desc ";

		return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}
	
}

?>