<?php

class RDFIOSPARQLEndpointTest extends RDFIOTestCase {

	protected function setUp() {
		parent::setUp();
	}

	protected function tearDown() {
		parent::tearDown();
	}

	public function testExtractQueryInfosAndTypeSelect() {
		$endpoint = new SPARQLEndpoint();

		$query = 'SELECT * WHERE { ?s ?p ?o }';

		list( $qInfo, $qType ) = $this->invokeMethod( $endpoint, 'extractQueryInfosAndType', array( $query ) );

		$this->assertEquals( $qType, 'select' );

		// Check some basic stuff that should be included in the parsed result
		$this->assertType( 'array', $qInfo );
		$this->assertEquals( 's', $qInfo['vars'][0] );
		$this->assertEquals( 'p', $qInfo['vars'][1] );
		$this->assertEquals( 'o', $qInfo['vars'][2] );
	}

	public function testExtractQueryInfosAndTypeConstruct() {
		$endpoint = new SPARQLEndpoint();

		$query = 'CONSTRUCT { ?s ?p ?o } WHERE { ?s ?p ?o }';

		list( $qInfo, $qType ) = $this->invokeMethod( $endpoint, 'extractQueryInfosAndType', array( $query ) );

		$this->assertEquals( $qType, 'construct' );

		// Check some basic stuff that should be included in the parsed result
		$this->assertType( 'array', $qInfo );
		$this->assertEquals( 's', $qInfo['vars'][0] );
		$this->assertEquals( 'p', $qInfo['vars'][1] );
		$this->assertEquals( 'o', $qInfo['vars'][2] );
	}

	public function testExtendQueryPatternsWithEquivUriLinks() {
		$endpoint = new SPARQLEndpoint();

		// Pattern corresponding to SPARQL query:
		// SELECT * WHERE { <http://ex.org/Sweden> ?p ?o }
		$patBefore = array(
			array(
				 'type' => 'triple',
				 's'  => 'http://example.org/onto/Sweden',
				 'p'  => 'p',
				 'o'  => 'o',
				 's_type' => 'uri',
				 'p_type' => 'var',
				 'o_type' => 'var',
				 'o_datatype' => '',
				 'o_lang' => ''
			)
		);

		$patExpected = array(
			array(
				'type' => 'triple',
				's'  => 'rdfio_var_0_s',
				'p'  => 'p',
				'o'  => 'o',
				's_type' => 'var',
				'p_type' => 'var',
				'o_type' => 'var',
				'o_datatype' => '',
				'o_lang' => ''
			),
			array(
				'type' => 'triple',
				's'  => 'rdfio_var_0_s',
				'p'  => 'http://www.w3.org/2002/07/owl#sameAs',
				'o'  => 'http://example.org/onto/Sweden',
				's_type' => 'var',
				'p_type' => 'uri',
				'o_type' => 'uri',
				'o_datatype' => '',
				'o_lang' => ''
			)
		);
		$patAfter = $this->invokeMethod( $endpoint, 'extendQueryPatternsWithEquivUriLinks', array( $patBefore ));

		$this->assertArrayEquals( $patExpected, $patAfter );
	}

	public function testExtendQueryPatternsWithEquivUriLinksProperty() {
		$endpoint = new SPARQLEndpoint();

		// Pattern corresponding to SPARQL query:
		// SELECT * WHERE { <http://ex.org/Sweden> ?p ?o }
		$patBefore = array(
			array(
				'type' => 'triple',
				's'  => 'http://example.org/onto/Sweden',
				'p'  => 'http://example.org/onto/HasCapital',
				'o'  => 'o',
				's_type' => 'uri',
				'p_type' => 'uri',
				'o_type' => 'var',
				'o_datatype' => '',
				'o_lang' => ''
			)
		);

		$patExpected = array(
			array(
				'type' => 'triple',
				's'  => 'rdfio_var_0_s',
				'p'  => 'rdfio_var_0_p',
				'o'  => 'o',
				's_type' => 'var',
				'p_type' => 'var',
				'o_type' => 'var',
				'o_datatype' => '',
				'o_lang' => ''
			),
			array(
				'type' => 'triple',
				's'  => 'rdfio_var_0_s',
				'p'  => 'http://www.w3.org/2002/07/owl#sameAs',
				'o'  => 'http://example.org/onto/Sweden',
				's_type' => 'var',
				'p_type' => 'uri',
				'o_type' => 'uri',
				'o_datatype' => '',
				'o_lang' => ''
			),
			array(
				'type' => 'triple',
				's'  => 'rdfio_var_0_p',
				'p'  => 'http://www.w3.org/2002/07/owl#equivalentProperty',
				'o'  => 'http://example.org/onto/HasCapital',
				's_type' => 'var',
				'p_type' => 'uri',
				'o_type' => 'uri',
				'o_datatype' => '',
				'o_lang' => ''
			)
		);
		$patAfter = $this->invokeMethod( $endpoint, 'extendQueryPatternsWithEquivUriLinks', array( $patBefore ));

		$this->assertArrayEquals( $patExpected, $patAfter );
	}

	public function testExtendQueryPatternsWithEquivUriLinksMuliplePatterns() {
		$endpoint = new SPARQLEndpoint();

		// Pattern corresponding to SPARQL query:
		// SELECT * WHERE { <http://ex.org/Sweden> ?p ?o }
		$patBefore = array(
			array(
				'type' => 'triple',
				's'  => 'http://example.org/onto/Sweden',
				'p'  => 'p',
				'o'  => 'o',
				's_type' => 'uri',
				'p_type' => 'var',
				'o_type' => 'var',
				'o_datatype' => '',
				'o_lang' => ''
			),
			array(
				'type' => 'triple',
				's'  => 'x',
				'p'  => 'y',
				'o'  => 'http://example.org/onto/Finland',
				's_type' => 'var',
				'p_type' => 'var',
				'o_type' => 'uri',
				'o_datatype' => '',
				'o_lang' => ''
			)
		);

		$patExpected = array(
			array(
				'type' => 'triple',
				's'  => 'rdfio_var_0_s',
				'p'  => 'p',
				'o'  => 'o',
				's_type' => 'var',
				'p_type' => 'var',
				'o_type' => 'var',
				'o_datatype' => '',
				'o_lang' => ''
			),
			array(
				'type' => 'triple',
				's'  => 'rdfio_var_0_s',
				'p'  => 'http://www.w3.org/2002/07/owl#sameAs',
				'o'  => 'http://example.org/onto/Sweden',
				's_type' => 'var',
				'p_type' => 'uri',
				'o_type' => 'uri',
				'o_datatype' => '',
				'o_lang' => ''
			),
			array(
				'type' => 'triple',
				's'  => 'x',
				'p'  => 'y',
				'o'  => 'rdfio_var_1_o',
				's_type' => 'var',
				'p_type' => 'var',
				'o_type' => 'var',
				'o_datatype' => '',
				'o_lang' => ''
			),
			array(
				'type' => 'triple',
				's'  => 'rdfio_var_1_o',
				'p'  => 'http://www.w3.org/2002/07/owl#sameAs',
				'o'  => 'http://example.org/onto/Finland',
				's_type' => 'var',
				'p_type' => 'uri',
				'o_type' => 'uri',
				'o_datatype' => '',
				'o_lang' => ''
			)
		);
		$patAfter = $this->invokeMethod( $endpoint, 'extendQueryPatternsWithEquivUriLinks', array( $patBefore ));

		$this->assertArrayEquals( $patExpected, $patAfter );
	}
}
