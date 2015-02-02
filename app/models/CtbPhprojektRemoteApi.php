<?php

use PhprojektRemoteApi\PhprojektRemoteApi;

class CtbPhprojektRemoteApi extends PhprojektRemoteApi
{

    /**
     * Proxy until api is implemented
     *
     * @return $this
     */
    public function getPtimecontrolApi()
    {
        return $this;
    }

    public function getProjectsApi()
    {
        return $this;
    }

    /**
     * @TODO: Use API when implemented getPtimecontrolApi
     *
     * @return string
     */
    public function getOvertimeOverall()
    {
        $pTimeControl = $this->httpClient->request(
            'GET',
            $this->phprojektUrl . '/ptimecontrol/ptc.php'
        );

        $xpath = '//*[@id="global-content"]/table/tr/td[4]';
        $filteredContent = $pTimeControl->filterXPath($xpath);
        return $filteredContent->html();
    }

    /**
     * @TODO: Use API when implemented getPtimecontrolApi
     *
     * @return string
     */
    public function getVacationDays()
    {
        // @TODO: Use API when implemented getPtimecontrolApi
        $pTimeControl = $this->httpClient->request(
            'GET',
            $this->phprojektUrl . '/vacation/index.php'
        );

        $xpath = '//*[@id="vacation_summary"]/div[2]/table/tr[3]/td[9]';
        $filteredContent = $pTimeControl->filterXPath($xpath);
        return $filteredContent->html();
    }

    /**
     * Get favorites by ProjectApi
     * @TODO: Remove when is available in project api
     *
     * @return array
     */
    public function getFavorites()
    {
        $pTimeControl = $this->httpClient->request(
            'GET',
            $this->phprojektUrl . '/timecard/timecard.php?submode=favorites'
        );

        $xpath = '//*[@id="left_container"]/div/div/form[2]/fieldset/table/tbody/*/td[1]';
        $filteredContent = $pTimeControl->filterXPath($xpath);
        $projectNodes = $filteredContent->extract(['_text']);

        return array_filter(array_map('trim', $projectNodes));
    }
}