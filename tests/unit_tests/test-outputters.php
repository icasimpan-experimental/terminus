<?php
/**
 * @file
 */

use Terminus\Outputters\JSONFormatter;
use Terminus\Outputters\StreamWriter;

/**
 * Class TestOutputters
 */
class TestOutputters extends PHPUnit_Framework_TestCase {

  protected $values;
  protected $records;
  protected $recordLabels;

  public function setUp() {
    $this->values = [
      'Integer' => 1234,
      'String' => 'abc',
      'Human String' => 'Hello, World!',
      'Nothing' => null,
      'Array' => ['foo', 'bar', 'baz']
    ];

    $this->records = [
      (object)[
        'foo' => 'abc',
        'bar' => 123,
        'baz' => 'extra'
      ],
      (object)[
        'foo' => 'def',
        'bar' => 456,
        'unlabled' => 'abc',
      ],
      (object)[
        'foo' => 'ghi',
        'bar' => 678,
        'biz' => 'another extra'
      ]
    ];

    $this->recordLabels = [
      'foo' => 'Foo',
      'bar' => 'Bar',
      'baz' => 'Extra 1',
      'biz' => 'Extra 2'
    ];
  }

  /**
   * @covers: \Terminus\Outputters\JSONFormatter
   */
  public function testJSONFormatter() {
    $formatter = new JSONFormatter();

    foreach ($this->values as $label => $value) {
      $formatted = $formatter->formatValue($value, $label);
      $this->assertEquals(json_encode($value), $formatted);
      $this->assertEquals($value, json_decode($formatted));
      $this->assertEquals(JSON_ERROR_NONE, json_last_error());
      // Make sure the human label is ignored.
      $this->assertNotContains($label, $formatted);
    }

    foreach ($this->records as $label => $value) {
      $formatted = $formatter->formatRecord($value, $label);
      $this->assertEquals(json_encode($value), $formatted);
      $this->assertEquals($value, json_decode($formatted));
      $this->assertEquals(JSON_ERROR_NONE, json_last_error());
      // Make sure the human label is ignored.
      $this->assertNotContains($label, $formatted);
    }


    $formatted = $formatter->formatValueList($this->values, array_keys($this->values));
    $this->assertEquals(json_encode($this->values), $formatted);
    $this->assertEquals($this->values, json_decode($formatted, TRUE));
    $this->assertEquals(JSON_ERROR_NONE, json_last_error());


    $formatted = $formatter->formatRecordList($this->records, $this->recordLabels);
    $this->assertEquals(json_encode($this->records), $formatted);
    $this->assertEquals($this->records, json_decode($formatted));
    $this->assertEquals(JSON_ERROR_NONE, json_last_error());
  }

  /**
   * @covers: \Terminus\Outputters\JSONFormatter
   */
  public function testStreamWriter() {
    // TODO: Use vfstream (https://github.com/mikey179/vfsStream) or similar to test writing to a stream.
//    $this->expectOutputString('Hello, World!');
//    $writer = new StreamWriter('php://stdout');
//    $writer->write('Hello, World!');
  }
}
