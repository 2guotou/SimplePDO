<?php

class SampleModel extends Model{
	
	function getAll(){
		return SimplePDO::instance()->doQuery("select * from `{this->table}`");
	}
}