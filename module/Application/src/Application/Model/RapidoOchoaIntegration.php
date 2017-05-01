<?php
namespace Application\Model;

use Zend\Soap\Client as SoapClient;
use Zend\View\Model\JsonModel;

class RapidoOchoaIntegration
{
    protected $client;
    protected $opts;

    protected $wsdlWSA = 'http://190.143.64.183:8081/SATServicespasterrestrepruebas/Service/WSVentaOnline.svc/basic?wsdl';
    protected $wsdl = 'http://190.143.64.183:8081/wsTarifasROpruebas/ServicioTarifas.asmx?wsdl';

    public function __construct($wsdl) {
        $opts = array(
            'soap_version' => SOAP_1_2,
            'encoding'=>'ISO-8859-1'
        );

        $client = new SoapClient($wsdl, $opts);

        $this->opts = $opts;
	    $this->client = $client;
    }

    public function getClientWS($wsdl = null, $options = null, $wsa = true, $action = null) {

        if (!$wsa) {
            $this->client = new SoapClient($wsdl, $this->opts);
        }
        $client = $this->client;

        switch ($wsa):
        	case true:
                $header =  new \SoapHeader("http://www.w3.org/2005/08/addressing",
                    "Action",
                    $action);
                $client->addSoapInputHeader($header);
                $header =  new \SoapHeader("http://www.w3.org/2005/08/addressing",
                    "To",
                    "http://190.143.64.183:8081/SATServicespasterrestrepruebas/Service/WSVentaOnline.svc");
                $client->addSoapInputHeader($header);
                break;
        	case false:
        	default:
        	    $username = $options['credentials']['userName'];
        	    $password = $options['credentials']['password'];

        	    $apiauth =array('UserName' => $username, 'Password'=>$password);

        	    $header =  new \SoapHeader("http://tempuri.org/", "CredencialUsuario", $apiauth, true);
        	    $client->addSoapInputHeader($header);
        	    break;
	    endswitch;

	    $this->client = $client;
	    return $this->client;
    }

    public function getOrigenes() {


//         $wsdl = 'http://190.143.64.183:8081/wsTarifasROpruebas/ServicioTarifas.asmx?wsdl';
        $options = array(
            'credentials' => array(
                'userName' => 'MVM',
                'password' => 'Mvm2007mvm!'
            )
        );
        $client = $this->getClientWS($this->wsdl, $options, false);

        /*
         * For proxy red et
        */
        // $client->setProxyHost('172.30.11.5');
        // $client->setProxyPort("8080");
        // $client->setProxyLogin("plirom");
        // $client->setProxyPassword("098iop+-*");

        $req = $client->obtenerLocalidadesActivas();

        $responseXml = $client->getLastResponse();
        $xml = simplexml_load_string($responseXml, NULL, NULL, "http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace("soap", "http://www.w3.org/2003/05/soap-envelope");
        $xml->registerXPathNamespace("xsi", "http://www.w3.org/2001/XMLSchema-instance");
        $xml->registerXPathNamespace("xsd", "http://www.w3.org/2001/XMLSchema");
        $xml->registerXPathNamespace("xs", "http://www.w3.org/2001/XMLSchema");
        $xml->registerXPathNamespace("msdata", "urn:schemas-microsoft-com:xml-msdata");
        $xml->registerXPathNamespace("diffgr", "urn:schemas-microsoft-com:xml-diffgram-v1");

        return json_encode($xml->xpath("//NewDataSet"));
    }

    public function getDestinos($pstrLocalidadOrigen) {
        $options = array(
            'parameters' => array(
                'pstrLocalidadOrigen' => $pstrLocalidadOrigen
            )
        );
        $client = $this->getClientWS(null, null, true, "http://tempuri.org/IWSVentaOnline/ConsultarDestinosDisponibles");

        /*
         * For proxy red et
        */
        // $client->setProxyHost('172.30.11.5');
        // $client->setProxyPort("8080");
        // $client->setProxyLogin("plirom");
        // $client->setProxyPassword("098iop+-*");
        $req = $client->__call("ConsultarDestinosDisponibles", $options);
        $responseXml = $client->getLastResponse();

        $xml = simplexml_load_string($responseXml);
        $cities = $xml->children("s", true)
        ->Body
        ->children(null)
        ->ConsultarDestinosDisponiblesResponse
        ->ConsultarDestinosDisponiblesResult
        ->children("b", true);

        return json_encode($cities);
    }

    public function getBloquearPuestos($idRodamiendo, $puesto, $disponible) {
        $options = array(
            'parameters' => array(
                'idRodamiento' => $idRodamiendo,
                'listaPuestos' => array(
                    'PuestoRodamientoTO' => array(
                        'Disponible' => $disponible,
                        'Puesto' => $puesto
                    )
                ),
                'idLocaOrigen' => '029',
                'idLocaDestino' => '001',
                'ipBloqueo' => '?'
            )
        );
        $client = $this->getClientWS(null, null, true, "http://tempuri.org/IWSVentaOnline/BloquearPuestosTemporal");

        /*
         * For proxy red et
        */
        // $client->setProxyHost('172.30.11.5');
        // $client->setProxyPort("8080");
        // $client->setProxyLogin("plirom");
        // $client->setProxyPassword("098iop+-*");
        $req = $client->__call("BloquearPuestosTemporal", $options);
        $responseXml = $client->getLastResponse();

        $xml = simplexml_load_string($responseXml);
        $response = $xml->children("s", true)
        ->Body
        ->children(null)
        ->BloquearPuestosTemporalResponse
        ->BloquearPuestosTemporalResult
        ->children("b", true);

        return json_encode($response);
    }

    public function getConsultarEstadoPuestos($origen, $destino, $numeroRodamiento) {

        $options = array(
            'parameters' => array(
                'strLocalidadOrigen' => $origen,
                'strLocalidadDestino' => $destino,
                'strNumeroRodamiento' => $numeroRodamiento
            )
        );

        $client = $this->getClientWS(null, null, true, "http://tempuri.org/IWSVentaOnline/ConsultarEstadoPuestos");

        $req = $client->__call("ConsultarEstadoPuestos", $options);
        $responseXml = $client->getLastResponse();

        $xml = simplexml_load_string($responseXml);
        $totalPuestos = $xml->children("s", true)
        ->Body
        ->children(null)
        ->ConsultarEstadoPuestosResponse
        ->ConsultarEstadoPuestosResult
        ->children("b", true)
        ->TotalPuestos;
        $puestos = $xml->children("s", true)
        ->Body
        ->children(null)
        ->ConsultarEstadoPuestosResponse
        ->ConsultarEstadoPuestosResult
        ->children("b", true)
        ->ListaPuestos
        ->children("b", true);

        return (array(
        	'totalPuestos' => $totalPuestos,
            'puestos' => $puestos
        ));
    }

    public function getObtenerTarifas($origen, $destino, $fecha) {
        $options = array(
            'parameters' => array(
                'strOrigen' => $origen,
                'strDestino' => $destino,
                'strFecha' => $fecha
            )
        );

        $client = $this->getClientWS(null, null, true, "http://tempuri.org/IWSVentaOnline/ObtenerTarifas");

        $req = $client->__call("ObtenerTarifas", $options);
        $responseXml = $client->getLastResponse();

        $xml = simplexml_load_string($responseXml);
        $rutas = $xml->children("s", true)
        ->Body
        ->children(null)
        ->ObtenerTarifasResponse
        ->ObtenerTarifasResult
        ->children("diffgr", true)
        ->diffgram
        ->children(null)
        ->NewDataSet;

        return (array(
            'rutas' => $rutas
        ));
    }
}