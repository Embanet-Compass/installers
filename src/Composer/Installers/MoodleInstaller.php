<?php
namespace Composer\Installers;

use Composer\Package\PackageInterface;

class MoodleInstaller extends BaseInstaller
{
    protected $locations = array(
        'core'               => '{$name}/',
        'mod'                => 'mod/{$name}/',
        'admin_report'       => 'admin/report/{$name}/',
        'atto'               => 'lib/editor/atto/plugins/{$name}/',
        'tool'               => 'admin/tool/{$name}/',
        'assignment'         => 'mod/assignment/type/{$name}/',
        'assignsubmission'   => 'mod/assign/submission/{$name}/',
        'assignfeedback'     => 'mod/assign/feedback/{$name}/',
        'auth'               => 'auth/{$name}/',
        'availability'       => 'availability/condition/{$name}/',
        'block'              => 'blocks/{$name}/',
        'booktool'           => 'mod/book/tool/{$name}/',
        'cachestore'         => 'cache/stores/{$name}/',
        'cachelock'          => 'cache/locks/{$name}/',
        'calendartype'       => 'calendar/type/{$name}/',
        'format'             => 'course/format/{$name}/',
        'coursereport'       => 'course/report/{$name}/',
        'datafield'          => 'mod/data/field/{$name}/',
        'datapreset'         => 'mod/data/preset/{$name}/',
        'editor'             => 'lib/editor/{$name}/',
        'enrol'              => 'enrol/{$name}/',
        'filter'             => 'filter/{$name}/',
        'gradeexport'        => 'grade/export/{$name}/',
        'gradeimport'        => 'grade/import/{$name}/',
        'gradereport'        => 'grade/report/{$name}/',
        'gradingform'        => 'grade/grading/form/{$name}/',
        'local'              => 'local/{$name}/',
        'logstore'           => 'admin/tool/log/store/{$name}/',
        'ltisource'          => 'mod/lti/source/{$name}/',
        'ltiservice'         => 'mod/lti/service/{$name}/',
        'message'            => 'message/output/{$name}/',
        'mnetservice'        => 'mnet/service/{$name}/',
        'plagiarism'         => 'plagiarism/{$name}/',
        'portfolio'          => 'portfolio/{$name}/',
        'qbehaviour'         => 'question/behaviour/{$name}/',
        'qformat'            => 'question/format/{$name}/',
        'qtype'              => 'question/type/{$name}/',
        'quizaccess'         => 'mod/quiz/accessrule/{$name}/',
        'quiz'               => 'mod/quiz/report/{$name}/',
        'report'             => 'report/{$name}/',
        'repository'         => 'repository/{$name}/',
        'scormreport'        => 'mod/scorm/report/{$name}/',
        'theme'              => 'theme/{$name}/',
        'tinymce'            => 'lib/editor/tinymce/plugins/{$name}/',
        'profilefield'       => 'user/profile/field/{$name}/',
        'webservice'         => 'webservice/{$name}/',
        'workshopallocation' => 'mod/workshop/allocation/{$name}/',
        'workshopeval'       => 'mod/workshop/eval/{$name}/',
        'workshopform'       => 'mod/workshop/form/{$name}/'
    );

    /**
     * Return the install path based on package type.
     *
     * @param  PackageInterface $package
     * @param  string           $frameworkType
     * @return string
     */
    public function getInstallPath(PackageInterface $package, $frameworkType = '')
    {
        $type = $this->package->getType();
        $extra = $package->getExtra();
        $parent_extra = $this->composer->getPackage()->getExtra();

        if( array_key_exists('moodle', $parent_extra) && array_key_exists( "folders", $parent_extra['moodle'] ) )
        {
            if($parent_extra['moodle']['folders']['webroot']['name']) $webroot_folder_name = $parent_extra['moodle']['folders']['webroot']['name'];
            $webroot_folder_name = 'moodle';
        } else {
            $webroot_folder_name = '.';
        }

        $prettyName = $this->package->getPrettyName();
        if (strpos($prettyName, '/') !== false) {
            list($vendor, $name) = explode('/', $prettyName);
        } else {
            $vendor = '';
            $name = $prettyName;
        }

        $availableVars = $this->inflectPackageVars(compact('name', 'vendor', 'type'));


        if (!empty($extra['installer-name'])) {
            $availableVars['name'] = $extra['installer-name'];
        }

        if ($this->composer->getPackage()) {
            $extra = $this->composer->getPackage()->getExtra();
            if (!empty($extra['installer-paths'])) {
                $customPath = $this->mapCustomInstallPaths($extra['installer-paths'], $prettyName, $type);
                if ($customPath !== false) {
                    return $this->templatePath($customPath, $availableVars);
                }
            }
        }

        $packageType = substr($type, strlen($frameworkType) + 1);
        $locations = $this->getLocations();
        if (!isset($locations[$packageType])) {
            throw new \InvalidArgumentException(sprintf('Package type "%s" is not supported', $type));
        }

        if($type == 'moodle-core') return $webroot_folder_name;
        else return $this->templatePath($webroot_folder_name.'/'.$locations[$packageType], $availableVars);
    }
}
