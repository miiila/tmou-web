<?php declare(strict_types=1);
namespace InstruktoriBrno\TMOU\Grids\TeamsGrid;

use InstruktoriBrno\TMOU\Grids\DataGridFactory;
use Ublaboo\DataGrid\DataSource\IDataSource;

class TeamsGridFactory
{
    /** @var DataGridFactory */
    private $dataGridFactory;

    public function __construct(DataGridFactory $dataGridFactory)
    {
        $this->dataGridFactory = $dataGridFactory;
    }

    public function create(int $eventNumber, IDataSource $dataSource): TeamsGrid
    {
        return new TeamsGrid($eventNumber, $dataSource, $this->dataGridFactory);
    }
}
