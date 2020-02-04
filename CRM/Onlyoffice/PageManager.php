<?php
/*-------------------------------------------------------+
| SYSTOPIA OnlyOffice Integration                        |
| Copyright (C) 2019 SYSTOPIA                            |
| Author: B. Zschiedrich (zschiedrich@systopia.de)       |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+-------------------------------------------------------*/

use CRM_Onlyoffice_ExtensionUtil as E;

/**
 * Manages presentation and order of the Forms/Pages.
 */
abstract class CRM_Onlyoffice_PageManager
{
    private const Pages = [
        'EntryPoint',
        self::AccountSelectionPageName,
        self::TemplateSelectionPageName,
        //self::DocumentEditorPageName,
        self::RunnerPageName,
        self::ResultPageName,
    ];

    private const PagePaths = [
        self::AccountSelectionPageName => 'civicrm/onlyoffice/generator/accountselection',
        self::TemplateSelectionPageName => 'civicrm/onlyoffice/generator/templateselection',
        //self::DocumentEditorPageName => 'civicrm/onlyoffice/generator/documenteditor',
        self::RunnerPageName => 'civicrm/onlyoffice/generator/runner',
        self::ResultPageName => 'civicrm/onlyoffice/generator/result',
    ];

    private const SessionKeyPrefix = 'onlyoffice_session';
    private const CurrentPageSessionKey = 'current_page';
    private const DataSessionKey = 'data';

    public const AccountSelectionPageName = 'AccountSelection';
    public const TemplateSelectionPageName = 'TemplateSelection';
    //public const DocumentEditorPageName = 'DocumentEditor';
    public const RunnerPageName = 'Runner';
    public const ResultPageName = 'Result';

    /**
     * Get the current page from the session.
     */
    private static function getCurrentPage(): ?string
    {
        $currentPage = CRM_Core_Session::singleton()->get(self::CurrentPageSessionKey, self::SessionKeyPrefix);

        return $currentPage;
    }

    /**
     * Store the current page in the session.
     */
    private static function setCurrentPage(string $page): void
    {
        CRM_Core_Session::singleton()->set(self::CurrentPageSessionKey, $page, self::SessionKeyPrefix);
    }

    private static function getAllData(): array
    {
        $allData = CRM_Core_Session::singleton()->get(self::DataSessionKey, self::SessionKeyPrefix);

        return $allData;
    }

    private static function setAllData(array $allData): void
    {
        CRM_Core_Session::singleton()->set(self::DataSessionKey, $allData, self::SessionKeyPrefix);
    }

    private static function convertPathToUrl(string $path): string
    {
        $url = CRM_Utils_System::url($path, NULL, FALSE, NULL, TRUE, FALSE, TRUE);

        return $url;
    }

    private static function redirectToPage(string $page): void
    {
        $pagePath = self::PagePaths[$page];
        $pageUrl = self::convertPathToUrl($pagePath);

        CRM_Utils_System::redirect($pageUrl);
    }

    /**
     * Open a page relative in position to the current page.
     * @param int $difference The difference in the position of both pages, negative values mean going backwards.
     */
    private static function openRelativePage(int $difference): void
    {
        $currentPage = self::getCurrentPage();
        $currentPageIndex = array_search($currentPage, self::Pages);

        $nextPageIndex = $currentPageIndex + $difference;

        if ($nextPageIndex != 0) // Going back to the entry point (index 0) is not possible.
        {
            $nextPage = self::Pages[$nextPageIndex];

            self::setCurrentPage($nextPage);
            self::redirectToPage($nextPage);
        }
    }

    /**
     * Start the session. \
     * This must be called by the entry point at startup instead of "openedPageIsCorrect".
     * NOTE: The entry point is no regular page but virtual. It is impossible to go back to it.
     */
    public static function startSession(): void
    {
        // Reset all session data:
        self::setAllData([]);

        // Set the page to the "virtual" entry point:
        $entryPoint = self::Pages[0];
        self::setCurrentPage($entryPoint);
    }

    public static function endSession(): void
    {
        // Reset all session data:
        self::setAllData([]);

        // TODO: Forward to the result or back or anything else.
    }

    /**
     * Must be called by all pages as soon as they are opened.
     * @param string $openedPage The page that has been opened, e.g. the caller of this function.
     * @return bool True if the opened page is the current page, otherwise false.
     */
    public static function openedPageIsCorrect(string $openedPage): bool
    {
        $currentPage = self::getCurrentPage();

        if ($currentPage == $openedPage)
        {
            return true;
        }
        else if ($currentPage !== null)
        {
            self::redirectToPage($currentPage);
        }
        else
        {
            // If we are here, the user has opened the URL directly without any entry point.
            // This is pointless. So we return him back to the start page.
            $pageUrl = self::convertPathToUrl('civicrm');
            CRM_Utils_System::redirect($pageUrl);
        }

        return false;
    }

    /**
     * Redirect to the next page.
     */
    public static function openNextPage(): void
    {
        self::openRelativePage(+1);
    }

    /**
     * Redirect to the previous page.
     */
    public static function openPreviousPage(): void
    {
        self::openRelativePage(-1);
    }

    /**
     * Get data stored in the session.
     * @param string $key The unique identifier for the data.
     * @return mixed The data for this key.
     */
    public static function getData(string $key)
    {
        $allData = self::getAllData();

        $data = $allData[$key];

        return $data;
    }

    /**
     * Store data in the session.
     * @param string $key The unique identifier of the data.
     * @param mixed $data The data to set for this key.
     */
    public static function setData(string $key, $data): void
    {
        $allData = self::getAllData();

        $allData[$key] = $data;

        self::setAllData($allData);
    }
}
