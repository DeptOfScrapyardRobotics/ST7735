<?php

namespace DeptOfScrapyardRobotics\Displays\ST77xx\Console;

use DeptOfScrapyardRobotics\Displays\ST77xx\Enums\ST77xxCatalogIc;
use Fabricate\Console\Command;
use GeneralPurposeIO\Circuits\CircuitRegistry;
use GeneralPurposeIO\Circuits\Console\Concerns\ScaffoldsCircuitProfiles;
use GeneralPurposeIO\Circuits\Support\CircuitAttributeInspector;
use GeneralPurposeIO\Contracts\Circuits\CircuitException;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'st77xx:make-profile')]
class ST77xxMakeProfileCommand extends Command
{
    use ScaffoldsCircuitProfiles;

    protected ?string $signature = 'st77xx:make-profile
                    {ic? : One of st7735, st7789, st7796}
                    {name? : Profile key to write into config/circuits.php}
                    {--protocol= : Protocol option label or factory name when non-interactive}';

    protected string $description = 'Scaffold a circuits.php profile for an ST77xx display IC';

    public function handle(CircuitRegistry $registry): int
    {
        $available = array_values(array_filter(
            ST77xxCatalogIc::slugs(),
            static fn (string $ic): bool => isset($registry->listCircuits()[$ic]),
        ));

        if ($available === []) {
            $this->components->error('No ST77xx ICs are registered. Is dept-of-scrapyard-robotics/st77xx discovered?');

            return self::FAILURE;
        }

        $ic = $this->argument('ic');
        if (is_null($ic) || $ic === '') {
            $ic = $this->choice('Which ST77xx IC?', $available);
        }

        $ic = (string) $ic;

        if (is_null(ST77xxCatalogIc::tryFrom($ic))) {
            $this->components->error("IC [{$ic}] is not an ST77xx panel (expected st7735, st7789, or st7796).");

            return self::FAILURE;
        }

        if (! isset($registry->listCircuits()[$ic])) {
            $this->components->error("Circuit [{$ic}] is not registered.");

            return self::FAILURE;
        }

        try {
            $class = $registry->resolveClass($ic);
            $options = CircuitAttributeInspector::protocolOptions($class);
        } catch (CircuitException $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $selected = $this->resolveProtocolOption($options);
        if (is_null($selected)) {
            return self::FAILURE;
        }

        $name = $this->argument('name');
        if (is_null($name) || $name === '') {
            $name = $this->ask('Profile name', $ic);
        }

        return $this->writePromptedProfile($ic, (string) $name, $selected);
    }
}
