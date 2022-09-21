#!/bin/bash
set -euo pipefail

tempdir=/tmp/documentation/codedeployupload

[[ -d $tempdir ]] && rm -r $tempdir
mkdir -p $tempdir

mkdir /tmp/$DEPLOYMENT_ID

echo "export tempdir=\"$tempdir\"" > "/tmp/$DEPLOYMENT_ID/.cdvariables";

if [ "$APPLICATION_NAME" == "documentation.stage.concretecms.org" ]
then
  echo "export projectdir=\"/home/forge/documentation.stage.concretecms.org\"" >> "/tmp/$DEPLOYMENT_ID/.cdvariables";
  echo "export deploydir=\"/home/forge/documentation.stage.concretecms.org/releases/$DEPLOYMENT_ID\"" >> "/tmp/$DEPLOYMENT_ID/.cdvariables";
elif [ "$APPLICATION_NAME" == "documentation.concretecms.org" ]
then
  echo "export projectdir=\"/home/forge/documentation.concretecms.org\"" >> "/tmp/$DEPLOYMENT_ID/.cdvariables";
  echo "export deploydir=\"/home/forge/documentation.concretecms.org/releases/$DEPLOYMENT_ID\"" >> "/tmp/$DEPLOYMENT_ID/.cdvariables";
fi
