<?php
require_once('MisConsultas.php');

class ci_desaprob_curso_ingreso_2018 extends toba_ci
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
                $anio = $filtro['ANIO_ACADEMICO'];
                $datos = self::get_datos_no_ingresantes($anio);
                $cuadro->set_datos($datos);
            } 
            else 
                $cuadro->limpiar_columnas();
	}

        //------------------------------------------------------------------------------------
        
        static function get_datos_no_ingresantes($anio) 
	{
            $db = MisConsultas::getConexionMini($anio);

            $sql = "SELECT  S.nombre AS sede, 
                            legajo,
                            apellido || ', ' || nombres AS inscripto,
                            dni,
                            carrera_nombre,
                            anio_ingreso,
                            e_mail,
                            CASE WHEN veces_reprob_matem = -1 THEN 'A'
                                ELSE veces_reprob_matem::VARCHAR
                            END AS veces_reprob_matem,
                            CASE WHEN veces_reprob_resol = -1 THEN 'A'
                                ELSE veces_reprob_resol::VARCHAR
                            END AS veces_reprob_resol,
                            CASE WHEN veces_reprob_ivu = -1 THEN 'A'
                                ELSE veces_reprob_ivu::VARCHAR
                            END AS veces_reprob_ivu
                        FROM rep_desaprobados R 
                        JOIN sga_sedes S ON (S.sede = R.sede)
                            WHERE anio_ingreso = $anio
                    ORDER BY 2"; 

            $datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            
            return $datos;
        }

    //-----------------------------------------------------------------------------------
    //---- Actualizar -------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function get_ultima_actualizacion()
    {
        $db = MisConsultas::getConexionMini();
        $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                    FROM rep_fecha_actualiz_tablas
                        WHERE tabla = 'rep_desaprobados'";
        $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
    }

    function evt__formulario__modificacion()
    {
        if (isset($this->s__datos_filtro))
        {   
            $anio = $this->s__datos_filtro['ANIO_ACADEMICO'];
            $db = MisConsultas::getConexionMini();
            $sql = "EXECUTE PROCEDURE sp_rep_desaprobados($anio)";
            $db->query($sql)->fetchAll();
        }
        else
        {
            $mensaje = "Debe seleccionar un año de ingreso";
            toba::notificacion()->agregar($mensaje);
        }
    }   

}
?>