<?php
require_once('MisConsultas.php');

class ci_listado_alumnos_fcex extends toba_ci
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
                $datos = $this->get_alumnos_fecx($this->s__datos_filtro);
            } 
            else 
            {
                $datos = $this->get_alumnos_fecx(NULL);
            }
            $cuadro->set_datos($datos);
        }

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
	}

    function get_alumnos_fecx($filtro)
    {
        (isset($filtro['busqueda'])) ? $filtro = $filtro['busqueda'] : $filtro = null;

        $sql = "SELECT DISTINCT P.apellido, P.nombres, P.nro_documento, P.fecha_nacimiento
                    FROM siu_guarani:sga_alumnos A, siu_guarani:sga_personas P
                    WHERE A.nro_inscripcion = P.nro_inscripcion
                    AND A.regular = 'S' AND A.calidad = 'A' ";
                    
        if (isset($filtro))
        {
            $sql .= " AND   (lower(P.apellido) LIKE lower('%$filtro%')
                            OR lower(P.nombres) LIKE lower('%$filtro%')
                            OR P.nro_documento LIKE '%$filtro%'
                            ) ";
        }

        $sql .= " UNION
                    SELECT DISTINCT P.apellido, P.nombres, P.nro_documento, P.fecha_nacimiento
                        FROM sga_alumnos A, sga_personas P
                        WHERE A.nro_inscripcion = P.nro_inscripcion
                        AND A.regular = 'S' AND A.calidad = 'A'
                        AND P.nro_documento NOT IN (
                                SELECT P2.nro_documento 
                                    FROM siu_guarani:sga_personas P2, siu_guarani:sga_alumnos A2
                                    WHERE P2.nro_inscripcion = A2.nro_inscripcion 
                                        AND A2.calidad = 'A' AND A2.regular = 'S'
                            ) ";
        if (isset($filtro))
        {
            $sql .= " AND   (lower(P.apellido) LIKE lower('%$filtro%')
                            OR lower(P.nombres) LIKE lower('%$filtro%')
                            OR P.nro_documento LIKE '%$filtro%'
                            )";
        }

        $sql .= " ORDER BY 1";
        
        $anio = date ('Y');
        $db = MisConsultas::getConexionMini($anio);
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}