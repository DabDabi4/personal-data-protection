<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Title;

class DocxPreviewController extends Controller
{
    public function show($filename)
    {
        $filePath = storage_path("app/public/{$filename}");

        if (!file_exists($filePath)) {
            abort(404, 'Файл не знайдено');
        }

        $phpWord = IOFactory::load($filePath);
        $html = '';
        $inList = false;
        $listType = null;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {

                // Заголовки
                if ($element instanceof Title) {
                    $level = $element->getDepth();
                    $text = $element->getText();
                    $html .= "<h{$level}>{$text}</h{$level}>";
                }

                // Параграфи з TextRun
                elseif ($element instanceof TextRun) {
                    $html .= '<p>';
                    foreach ($element->getElements() as $subElement) {
                        if ($subElement instanceof Text) {
                            $html .= $subElement->getText();
                        }
                    }
                    $html .= '</p>';
                }

                // Списки
                elseif ($element instanceof ListItem) {
                    if (!$inList) {
                        $listType = $element->getStyle()->getListType();
                        $html .= $listType === \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER ? '<ol>' : '<ul>';
                        $inList = true;
                    }
                    $html .= '<li>' . $element->getText() . '</li>';
                } else {
                    if ($inList) {
                        $html .= $listType === \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER ? '</ol>' : '</ul>';
                        $inList = false;
                    }

                    if (method_exists($element, 'getText')) {
                        $html .= '<p>' . $element->getText() . '</p>';
                    }

                    if ($element instanceof Table) {
                        $html .= '<table class="table-auto border border-collapse border-gray-500">';
                        foreach ($element->getRows() as $row) {
                            $html .= '<tr>';
                            foreach ($row->getCells() as $cell) {
                                $cellText = '';
                                foreach ($cell->getElements() as $cellElement) {
                                    if (method_exists($cellElement, 'getText')) {
                                        $cellText .= $cellElement->getText();
                                    }
                                }
                                $html .= '<td class="border p-2">' . $cellText . '</td>';
                            }
                            $html .= '</tr>';
                        }
                        $html .= '</table>';
                    }
                }
            }

            if ($inList) {
                $html .= $listType === \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER ? '</ol>' : '</ul>';
                $inList = false;
            }
        }

        return view('docx.preview', ['html' => $html]);
    }
}
