<?php
require_once 'MisConsultas.php';

class ci_inscriptos_x_carrera extends toba_ci
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
                $datos = $this->get_inscriptos_carrera($this->s__datos_filtro);
            } 
            else 
            {
                $datos = $this->get_inscriptos_carrera();
            }
            $cuadro->set_datos($datos);
	}
        
        //---- Consultas -----------------------------------------------------------------------

        function get_inscriptos_carrera($filtro=array())
        {
            $db = MisConsultas::getConexion();
            $sql = "SELECT  P.nro_documento, 
                            P.apellido || ', ' || P.nombres AS alumno, 
                            CASE    WHEN P.sexo = 1 THEN 'M'
                                    WHEN P.sexo = 2 THEN 'F'
                            END AS genero, 
                            C.nombre AS carrera,
                            I.anio_academico,
                            A.regular, 
                            CASE    WHEN A.calidad = 'A' THEN 'Activo'
                                    WHEN A.calidad = 'E' THEN 'Egresado'
                                    WHEN A.calidad = 'N' THEN 'Abandono'
                                    WHEN A.calidad = 'E' THEN 'Pasivo'
                            END AS calidad
                    FROM sga_personas P
                    JOIN sga_carrera_aspira S ON (S.nro_inscripcion = P.nro_inscripcion)
                    JOIN sga_periodo_insc I ON (I.periodo_inscripcio = S.periodo_inscripcio)
                    JOIN sga_alumnos A ON (A.carrera = S.carrera AND A.nro_inscripcion = S.nro_inscripcion)
                    JOIN sga_carreras C ON (C.carrera = A.carrera) 
                    WHERE C.estado = 'A' ";
            if (isset($filtro['CARRERA']))
            {
                $carrera = $filtro['CARRERA'];
                $sql .= " AND C.carrera = '$carrera' ";
            }    
            $sql .= " ORDER BY C.carrera, I.anio_academico DESC";
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);            
        }
}

?>