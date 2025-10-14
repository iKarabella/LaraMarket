<?php

namespace App\Services\ManticoreSearch;

class Builder
{
    private ?string $limit = null;
    private array $columns = [];
    private array $where = [];
    private string $match;
    private array $options = [
        'fuzzy'=>1, 
        //'layouts'=>'us,ru', 
        'distance'=>2
    ];

    public function __construct(private string $table)
    {}

    public function build(){
        $columns = count($this->columns) ? implode(', ', $this->columns) : '*';

        $where = '';

        if($this->match) $where.="WHERE MATCH('{$this->match}')";

        foreach ($this->where as $key=>$exp) 
        {
            if ($key>0) $exp->or ? $where.=' OR ':' AND ';
            $where.=$exp;
        }

        if (empty($where)) $where='1';

        if ($this->limit) $where.=$this->limit;

        if (count($this->options))
        {
            $options=[];
            foreach ($this->options as $oKey=>$oVal) $options[] = "$oKey = $oVal";
            $where.=" OPTION ".implode(', ', $options);
        }

        return "SELECT $columns FROM {$this->table} $where;";
    }

    public function where($exp){
        $this->where[]=['or'=>false, 'exp'=>$exp];
        return $this;
    }

    public function limit(int $limit, ?int $page=null)
    {
        $this->limit = " LIMIT $limit";

        if ($page>0) $this->limit.=' OFFSET '.($page>1 ? $page*$limit : 0);

        return $this;
    }

    public static function table($table){
        return new self($table);
    }

    public function match($query)
    {
        $this->match=$query;
        
        return $this;
    }

    public function select(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function filters(?array $filters=null)
    {
        if ($filters) foreach ($filters as $filter=>$val)
        {
            //TODO фильтры в мантикоре
        }
        
        return $this;
    }
}