<?php

namespace App\Command;

use App\Entity\Company;
use App\Entity\Contract;
use App\Entity\Employee;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import',
    description: 'Add a short description for your command',
)]
class ImportCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->em = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::OPTIONAL, 'fitxategia')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        if (!$file) {
            $io->error(sprintf('Fitxategia zehaztea beharrezkoa da.'));
            return Command::FAILURE;
        }

        /** Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $sheetCount = $spreadsheet->getSheetCount();

        $io->note($sheetCount);

        for ($i=1; $i < $sheetCount; $i++) {
            $sheet = $spreadsheet->getSheet($i);
            $sheetTitle = $sheet->getTitle();
            $io->note('index: ' . $i . ' irakurtzen => ' . $sheet->getTitle()  );
            $sheetData = $sheet->toArray(null, true, true, true);
            if ($sheetData[4]['A'] === null) {
                $io->error("Begiratu $sheetTitle izena ez dago A4 eremuan");
                return Command::FAILURE;
            }
            $izena = $sheetData[4]['A'];
            $employee = new Employee();
            $employee->setName($izena);
            $this->em->persist($employee);

            $totalYears = 0;
            $totalMonths = 0;
            $totalDays = 0;

            $rowCount = count($sheetData);

            for ( $k= 6; $k < $rowCount; $k++) {
                if ( $sheetData[$k]['B'] === 'TOTALES' ) {

                    $totalYears = round($totalDays / 365);
                    $totalMonths = floor(($totalDays - ($totalYears * 365)) / 30.4);
                    $totDays = floor($totalDays - ($totalYears * 365) - ($totalMonths * 30.4));
                    $io->writeln($totalYears . '--' . $totalMonths . '--' . $totDays);

                    $employee->setYears($totalYears);
                    $employee->setMonths($totalMonths);
                    $employee->setDays($totDays);
                    $this->em->persist($employee);
                    $this->em->flush();

                }
                if ( $sheetData[$k]['A'] === null ) {
                    continue;
                }

                $companyName = $sheetData[$k]['A'];
                $start = $sheetData[$k]['B'];
                $end = $sheetData[$k]['C'];
                $days  = $sheetData[$k]['D'];

                $contract = new Contract();
                $contract->setName($companyName);
                $contract->setEmployee($employee);
                $startDate = "";
                $hasStart = true;
                $hasEnd = true;
                if ( $start !== null ) {
                    try {
                        $startDate = Carbon::createFromFormat('Y/d/m', $start);
                        $contract->setStartDate($startDate);
                    } catch (\Exception $exception) {
                        try {
                            $startDate = Carbon::createFromFormat('d/m/Y', $start);
                            $contract->setStartDate($startDate);
                        } catch (\Exception $exception) {
                            $startDate = Carbon::createFromFormat('Y-m-d', $start);
                            $contract->setStartDate($startDate);
                        }
                    }
                } else {
                    $hasStart = false;
                }
                $endDate = "";
                if ( $end !== null ) {
                    try {
                        $endDate = Carbon::createFromFormat('Y/d/m', $end);
                        $contract->setEndDate($endDate);
                    } catch (\Exception $exception) {
                        try {
                            $endDate = Carbon::createFromFormat('d/m/Y', $end);
                            $contract->setEndDate($endDate);
                        } catch (\Exception $exception) {
                            $endDate = Carbon::createFromFormat('Y-m-d', $end);
                            $contract->setEndDate($endDate);
                        }
                    }
                } else {
                    $hasEnd = false;
                }

                $diff = 0;
                if ( $hasStart && $hasEnd ) {
                    $diff = $endDate->diffInDays($startDate);
                    $io->writeln("Index => $k");
                    $io->writeln($sheetData[$k]['A'] . ' ' . $sheetData[$k]['D']. ' ' . $k);
                    $io->writeln("Start => $start");
                    $io->writeln("End => $start");
                    $io->writeln($startDate->format('Y/m/d') . ' ' . $endDate->format('Y/m/d'));
                    $io->writeln("Egunak => $diff");
                    $io->writeln('');
                    $io->writeln('');
                }

                $totalDays += $diff;
                $contract->setDays($diff);
                $this->em->persist($contract);
                $this->em->flush();
            }
        }

        $io->success('Prozesua amaitu da.');

        return Command::SUCCESS;
    }
}
