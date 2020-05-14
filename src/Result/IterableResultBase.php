<?php

namespace Drupal\search_suggestions\Result;

abstract class IterableResultBase extends ResultBase implements IterableResultInterface {

  /**
   * {@inheritdoc}
   */
  public function current() {
    return current($this->result);
  }

  /**
   * {@inheritdoc}
   */
  public function next() {
    return next($this->result);
  }

  /**
   * {@inheritdoc}
   */
  public function key() {
    return key($this->result);
  }

  /**
   * {@inheritdoc}
   */
  public function valid() {
    return array_key_exists($this->key(), $this->result);
  }

  /**
   * {@inheritdoc}
   */
  public function rewind() {
    return rewind($this->result);
  }

}
