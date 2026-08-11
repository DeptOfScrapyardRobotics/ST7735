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

    /**
     * Stream pixel / payload bytes. Accepts packed binary strings (DumpedBuffer::raw_data)
     * or int byte arrays (boot/register helpers).
     *
     * @param  array<int, int>|string  $data
     */
    public function spiData(array|string $data = []): void
    {
        if (is_string($data)) {
            $length = strlen($data);
            $offset = 0;

            while ($offset < $length) {
                $this->dc->high();
                $this->spi->write(substr($data, $offset, $this->max_packet_size));
                $offset += $this->max_packet_size;
            }

            return;
        }

        foreach (array_chunk($data, $this->max_packet_size) as $chunk) {
            $this->dc->high();
            $this->spi->write($chunk);
        }
    }
}