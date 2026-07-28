<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\Concerns;

trait ST77xxIO
{
    public function spiCommand(int $register, array $command_data = []): int
    {
        $this->dc->low();
        $results = $this->spi->write([$register]);
        if(count($command_data) > 0){
            $this->spiData($command_data);
        }

        return $results;
    }

    public function spiData(array $data = []): void
    {
        foreach (array_chunk($data, $this->max_packet_size) as $chunk) {
            $this->dc->high();
            $this->spi->write($chunk);
        }
    }
}