<?php
require_once('MisConsultas.php');

class ci_cuentas_email extends toba_ci
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

    function get_mail_alumnos($filtro = null)
    {
        $sql = "SELECT  A.nro_inscripcion, 
                        A.apellido || ', ' || A.nombres AS alumno, 
                        P.nro_documento, 
                        A.usuario 
                FROM aux_casillascorreo A, sga_personas P
                    WHERE A.nro_inscripcion = P.nro_inscripcion ";
        if (isset($filtro))
        {
            $sql .= "AND (A.nro_inscripcion LIKE '%$filtro%'
                        OR lower(A.apellido) LIKE lower('%$filtro%')
                        OR lower(A.nombres) LIKE lower('%$filtro%') 
                        OR P.nro_documento LIKE '%$filtro%' ) ";
        }
        $sql .= "ORDER BY 2";

        $db = MisConsultas::getConexion();
        $datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }

    function conf__cuadro(toba_ei_cuadro $cuadro)
    {
        if (isset($this->s__datos_filtro)) 
        {
            $filtro = $this->s__datos_filtro['filtro'];
            $datos = self::get_mail_alumnos($filtro);
        } 		
        else
        {
            $datos = self::get_mail_alumnos();
        }
        $cuadro->set_datos($datos);
    }

}

?>