<?php
class dt_vw_planta_funcional extends toba_datos_tabla
{
	function get_listado($filtro=array())
	{
            $anio = date("Y");
            $sql = "SELECT  DISTINCT D.documento, 
                            D.legajo, 
                            V.apellido_nombres 
                        FROM vw_planta_funcional V
                        JOIN docentes D ON (D.id = V.docente_id) 
                        WHERE V.anio_academico_nombre = '$anio' ";

            if (isset($filtro['apellido_nombres'])) 
            {
                $sql .= " AND apellido_nombres ILIKE ".quote("%{$filtro['apellido_nombres']}%");
            }

            $sql .= " ORDER BY apellido_nombres ";

            return toba::db('Docentes')->consultar($sql);
	}
        
}
?>