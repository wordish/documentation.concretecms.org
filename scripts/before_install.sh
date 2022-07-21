#!/bin/bash
set -euo pipefail

tempdir=/tmp/codedeployupload

[[ -d $tempdir ]] && rm -r $tempdir
mkdir -p $tempdir

if [ "$APPLICATION_NAME" == "documentation.stage.concretecms.org" ]
then
  echo "export projectdir=\"/home/forge/documentation.stage.concretecms.org\"" > "/tmp/.cdvariables";
  echo "export deploydir=\"/home/forge/documentation.stage.concretecms.org/releases/$DEPLOYMENT_ID\"" >> "/tmp/.cdvariables";
elif [ "$APPLICATION_NAME" == "documentation.concretecms.org" ]
then
  echo "export projectdir=\"/home/forge/documentation.concretecms.org\"" > "/tmp/.cdvariables";
  echo "export deploydir=\"/home/forge/documentation.concretecms.org/releases/$DEPLOYMENT_ID\"" >> "/tmp/.cdvariables";
fi
