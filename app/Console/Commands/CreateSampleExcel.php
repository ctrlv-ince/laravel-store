<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CreateSampleExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:create-sample';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a sample Excel file for importing items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers
        $headers = ['item_name', 'item_description', 'price', 'quantity', 'groups'];
        $sheet->fromArray($headers, null, 'A1');

        // Sample data
        $data = [
            [
                'Arduino Uno R3',
                'The Arduino Uno is an open-source microcontroller board based on the Microchip ATmega328P microcontroller.',
                24.99,
                10,
                'Electronics,Microcontrollers'
            ],
            [
                'Raspberry Pi 4 Model B',
                'The Raspberry Pi 4 Model B is the latest product in the popular Raspberry Pi range of computers.',
                45.99,
                5,
                'Electronics,Computers,Single Board Computers'
            ],
            [
                'ESP32 Development Board',
                'ESP32 is a series of low-cost, low-power system on a chip microcontrollers with integrated Wi-Fi and dual-mode Bluetooth.',
                12.50,
                20,
                'Electronics,Microcontrollers,Wireless'
            ],
            [
                'Breadboard 830 Points',
                'A breadboard is a construction base for prototyping of electronics.',
                8.99,
                15,
                'Electronics,Components'
            ],
            [
                'Jumper Wires Pack',
                'A set of male-to-male, male-to-female, and female-to-female jumper wires.',
                6.99,
                30,
                'Electronics,Components,Accessories'
            ]
        ];

        // Add data starting from row 2
        $sheet->fromArray($data, null, 'A2');

        // Style the headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Style the data
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];

        $sheet->getStyle('A2:E' . (count($data) + 1))->applyFromArray($dataStyle);

        // Auto-size columns
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Save the file
        $writer = new Xlsx($spreadsheet);
        $filename = 'sample_items_import.xlsx';
        $writer->save($filename);

        $this->info("Sample Excel file created successfully: {$filename}");
        $this->info("The file contains sample data for 5 items with all required fields.");
        $this->info("You can find the file in your project root directory.");
    }
}
