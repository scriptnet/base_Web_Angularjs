<?php

session_start();
$user = $_SESSION['user'];

if(!isset($_SESSION['user']) || empty($_SESSION['user'])){
    header("location: ../../");
  exit;
};
//================================================
//    SACAMOS EL ID DE LA ISNTITUCIÓN
//================================================
require_once("../config/class.Database.php");
$sql="SELECT id_Institucion FROM tbl_usuario WHERE Us_Codigo = '$user'";
$RESPUESTA  = Database::get_row( $sql ); 
$institucion = $RESPUESTA["id_Institucion"];

//=================================================
//  RECIBIMOS LOS DATOS
//=================================================
$postdata = file_get_contents("php://input");
$jsonarray = json_decode($postdata, true);
//=================================================
//  CREAMOS VARIABLES PARA RECOGER LAS VARIABLES
//  DE EXISTENTES Y DNI NO VALIDOS
//=================================================
$T_Insertado= Array();
$Dnifalse = Array();
//=================================================
//RECORREMOS EL ARRAY
//=================================================

foreach ($jsonarray as $row) {
  $Dni = $row['Dni'];
  $Cod = $row['Dni'];
  $Fecha = $row['Fecha'];
  $Carrera = $row['Carrera'];
  $fechaUnix= 25569;
  $formula = ($Fecha-$fechaUnix)*86400;
  $resultadoFecha = gmdate("Y/m/d", $formula);
 

  $rptaT = Consulta($Dni);
  if ($rptaT) {
    $hecho = true;
    //si el titulado existe entonces buscame si existe en tbldetalle
    $rptaTD = ConsultaDetall($Cod, $institucion, $Carrera);
    if (!$rptaTD) {
      $rptaTD = InsertarDetall($institucion,$Cod,$resultadoFecha,$Carrera);
      $T_Insertado[]=Array(
			  'dni'=> $Cod,
			  'fecha'=> $resultadoFecha,
			  'carrera'=> $Carrera
			);
    }
  } else {
    //si el titulado no existe se va a agregar
    $rpta  = Database::apiReniec( $Dni );
    if ($rpta[0]['err'] && $rpta[1]['err'] ) {
        $paterno = $rpta[0]['PATERNO'];
        $materno = $rpta[0]['MATERNO'];
        $nombres = $rpta[0]['NOMBRES'];
        $sql = "INSERT INTO tbl_titulado (T_Nombres, T_Apellidos, T_Dni)
        VALUES(
              '$nombres', 
              '$paterno $materno', 
              '$Dni'
              )";
        $hecho = Database::ejecutar_idu( $sql );
        
        $rptaTD = InsertarDetall($institucion,$Cod,$resultadoFecha,$Carrera);
        $T_Insertado[]=Array(
          'dni'=> $Cod,
          'fecha'=> $resultadoFecha,
          'carrera'=> $Carrera
        );
        
    }else{
      //si el dni no existe
      $hecho = true;
      $Dnifalse []= array(
        'dni'=> $Dni,
        'fecha'=> $resultadoFecha,
        'carrera'=> $Carrera
      );
    }
      
  }
  

};

//=====================================================
//Verificamos si el Dni existe
//=====================================================
function Consulta ($Dni){
  $sql1="SELECT T_Dni FROM tbl_titulado WHERE T_Dni = '$Dni'";
  $respuesta  = Database::get_row( $sql1 );
   if (isset($respuesta['T_Dni'])) {
    $estado = true;
  } else {
    $estado = false;
  }
  return $estado;
};
//=====================================================
//Verificamos si el Dni en detalle existe
//=====================================================
function ConsultaDetall ($Cod, $institucion, $Carrera){
  $sql2="SELECT Cod_Dit FROM tbl_detalle_it WHERE Cod_Dit = '$Cod' AND id_Institucion = '$institucion' AND DIT_Carrera = '$Carrera'";
  $respuesta  = Database::get_row( $sql2 );
   if (isset($respuesta["Cod_Dit"])) {
    $estado = true;
  } else {
    $estado = false;
  }
  return $estado;
};
//=====================================================
//insertamos datos en la tabla detalles
//=====================================================
function InsertarDetall ($institucion,$Cod,$resultadoFecha,$Carrera){
  $sql3 = "INSERT INTO tbl_detalle_it 
                      (id_Institucion,
                       Cod_Dit,DIT_Fecha,
                       DIT_Carrera,
                       DIT_Estado)
                       VALUES(
                      '$institucion',
                      '$Cod',
                      '$resultadoFecha',
                      '$Carrera', 
                      'Pendiente')";
         $rpta = Database::ejecutar_idu( $sql3 );
  return $rpta;
};



if (is_numeric($hecho) OR $hecho === true) {
  $respuesta = array ( 'err'=>false, 'Mensaje'=>'Registro Insertado', 'T_Insertado'=>$T_Insertado, 'dniNull'=> $Dnifalse);
}

echo json_encode($respuesta);

?>