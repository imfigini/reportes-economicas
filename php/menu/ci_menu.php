<?php
class ci_menu extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

    function conf__arbol(toba_ei_arbol $arbol)
    {
        //-- Se obtienen los nodos que formar�n parte del arbol
        require_once('contrib/catalogo_items_menu/toba_catalogo_items_menu.php');
        $catalogo = new toba_catalogo_items_menu();
        $raiz = '1';        
        $catalogo->cargar(array(), $raiz);
        $nodos = $catalogo->get_hijos($raiz);

//ei_arbol($catalogo);
//ei_arbol($nodos);
//var_dump($nodos); die;

        //-- Se configura el arbol
        $arbol->set_mostrar_filtro_rapido(true);
        $arbol->set_mostrar_ayuda(false);       
        $arbol->set_nivel_apertura(1);
        $arbol->set_ancho_nombres(150);
        //$arbol->set_mostrar_utilerias(true);
        //$arbol->set_mostrar_propiedades_nodos(true);
        $arbol->set_datos($nodos);
        //var_dump ($arbol);
    }
    
    
/*    
    function conf__cuadro(toba_ei_cuadro $cuadro)
	{
            $this->get_menu();	
            $cuadro->set_datos($this->dep('datos')->tabla('prueba')->get_listado());
	}

        function get_menu()
        {
            $sql = "SELECT item, padre, carpeta, nombre, orden 
                        FROM apex_item
                        WHERE proyecto = 'Reportes'
                        AND carpeta = 1
                        AND padre = '1'
                        AND item <> '1'
                        ORDER BY orden";
            $carpetas = toba::db('toba_2_7')->consultar($sql);
            foreach ($carpetas AS $carpeta)
            {
                ei_arbol($carpeta['nombre'], 'nombre');
                $item = $carpeta['item'];
                $sql = "SELECT item, padre, carpeta, nombre, orden
                            FROM desarrollo.apex_item
                            WHERE padre = '$item'
                            ORDER BY padre, orden;";
                $operaciones = toba::db('toba_2_7')->consultar($sql);
                ei_arbol($operaciones, 'operaciones');
            }
            //return Connect::getConexion($parametros[0]);
        }*/

}
