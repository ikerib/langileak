<?php

namespace App\Command;

use App\Entity\Employee;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:orokorra',
    description: 'Add a short description for your command',
)]
class OrokorraCommand extends Command
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

        $io->writeln("");
        $io->writeln('OROKORRA orria irakurtzen');
        $sheet = $spreadsheet->getSheet(0);
        $sheetData = $sheet->toArray(null, true, true, true);

        $rowCount = count($sheetData);

        for ( $k= 6; $k < $rowCount; $k++) {
            $io->writeln($sheetData[$k]['B']);
            $employee = $this->em->getRepository(Employee::class)->findOneByName($sheetData[$k]['B']);
            if ($employee) {
                $strHasi = $sheetData[$k]['C'];
                $strTeorikoa = $sheetData[$k]['E'];
                $strHiruUrtekoa = $sheetData[$k]['F'];
                $strHilabetea = $sheetData[$k]['G'];

                $dHasi = null;
                $dTeorikoa = null;
                $dHiruUrtekoa = null;
                $dHilabetea = null;

                if ($strHasi !== null) {
                    try {
                        $dHasi = Carbon::createFromFormat('Y/d/m', $strHasi);
                    } catch (\Exception $exception) {
                        try {
                            $dHasi = Carbon::createFromFormat('d/m/Y', $strHasi);
                        } catch (\Exception $exception) {
                            $dHasi = Carbon::createFromFormat('Y-m-d', $strHasi);
                        }
                    }
                }

                if ($strTeorikoa !== null) {
                    try {
                        $dTeorikoa = Carbon::createFromFormat('Y/d/m', $strTeorikoa);
                    } catch (\Exception $exception) {
                        try {
                            $dTeorikoa = Carbon::createFromFormat('d/m/Y', $strTeorikoa);
                        } catch (\Exception $exception) {
                            $dTeorikoa = Carbon::createFromFormat('Y-m-d', $strTeorikoa);
                        }
                    }
                }

                if ($strHiruUrtekoa !== null) {
                    try {
                        $dHiruUrtekoa = Carbon::createFromFormat('Y/d/m', $strHiruUrtekoa);
                    } catch (\Exception $exception) {
                        try {
                            $dHiruUrtekoa = Carbon::createFromFormat('d/m/Y', $strHiruUrtekoa);
                        } catch (\Exception $exception) {
                            $dHiruUrtekoa = Carbon::createFromFormat('Y-m-d', $strHiruUrtekoa);
                        }
                    }
                }

                if ( $strHilabetea !== null ) {
                    try {
                        $dHilabetea = Carbon::createFromFormat('Y/d/m', $strHilabetea);
                    } catch (\Exception $exception) {
                        try {
                            $dHilabetea = Carbon::createFromFormat('d/m/Y', $strHilabetea);
                        } catch (\Exception $exception) {
                            $dHilabetea = Carbon::createFromFormat('Y-m-d', $strHilabetea);
                        }
                    }
                }

                $employee->setDateStartWorking($dHasi);
                $employee->setDateTeoric($dTeorikoa);
                $employee->setDateTriennium($dHiruUrtekoa);
                $employee->setDatePayroll($dHilabetea);
                $this->em->persist($employee);
                $this->em->flush();
            }
        }

        $io->success('Prozesua amaitu da.');

        return Command::SUCCESS;
    }
}
