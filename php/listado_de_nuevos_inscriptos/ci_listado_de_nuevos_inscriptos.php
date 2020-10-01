<?php
require_once('MisConsultas.php');

class ci_listado_de_nuevos_inscriptos extends toba_ci
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
			$datos = $this->get_listado_nuevos_inscriptos($this->s__datos_filtro);
			$cuadro->set_datos($datos);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	function get_listado_nuevos_inscriptos($filtro=array())
	{
        if (!array_key_exists('ANIO_ACADEMICO', $filtro)) 
        {
			echo "Se produjo un error en la linea ".__line__." del archivo ".__file__.": Falta parámetro ANIO_ACADEMICO";
            return;
        } 

        $anio_academico = $filtro['ANIO_ACADEMICO'];

        $db = MisConsultas::getConexion ();

        $sqlText = "SELECT DISTINCT S.nombre AS sede, 
                                    legajo, 
                                    (apellido || ', ' || nombres) AS nombres, 
                                    dni, 
                                    fecha_nacim,
                                    e_mail, 
                                    ciudad_proced, 
                                    prov_proced, 
                                    colegio_secundario, 
                                    ciudad_colegio, 
                                    prov_colegio
                        FROM rep_nuevos_inscriptos R
                        JOIN sga_sedes S ON (S.sede = R.sede)";

        if ($anio_academico != null) 
        {
            $sqlText .= "WHERE anio_ingreso = '$anio_academico' ";
        }

        $sqlText .= "ORDER BY nombres";

        $datos = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);

        $datos = MisConsultas::addFakeId($datos);
        $alumnos = $this->agregaCarreras($db, $datos, $anio_academico);

        return $alumnos;
    }	

	function agregaCarreras($db, $datos, $anio_academico) 
    {
        $resultado = array();

        foreach ($datos as $dato) 
        {
            $legajo = $dato['LEGAJO'];
            $carreras = $this->getInfoAlumnoCarrera($db, $legajo, $anio_academico);
            $i = 1;
            foreach ($carreras as $carrera) 
            {
                $dato["CARRERA".$i] = $carrera['CARRERA'];
                $i++;
            }
            $resultado[] = $dato;
		}
        return $resultado;
	}	
	
	function getInfoAlumnoCarrera($db, $legajo, $anio_academico) 
    {
        $sqlText = "SELECT C.nombre_reducido AS carrera, YEAR(A.fecha_ingreso)
                        FROM sga_alumnos A, sga_carreras C
                        WHERE A.legajo = '$legajo'
                            AND YEAR(A.fecha_ingreso) = '$anio_academico'
                            AND A.carrera = C.carrera";

        $carreras = $db->query($sqlText);
        if ($carreras != False) 
        {
            $carreras = $carreras->fetchAll(PDO::FETCH_ASSOC);
            return $carreras;
        }
        else
        {	
            echo "Se produjo un error en la linea ".__line__." del archivo ".__file__;
        }
    }


	//-----------------------------------------------------------------------------------
    //---- procesar ---------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function get_ultima_actualizacion()
    {
        $db = MisConsultas::getConexion();
        $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                    FROM rep_fecha_actualiz_tablas
                        WHERE tabla = 'rep_nuevos_inscriptos'";
        $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
    }

    function evt__procesar__procesar()
    {
        $db = MisConsultas::getConexion();
        $sql = "EXECUTE PROCEDURE 'dba'.sp_rep_nuevos_inscriptos()";
        $db->query($sql)->fetchAll();
    }   

}
