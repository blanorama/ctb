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
}