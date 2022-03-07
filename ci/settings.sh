#!/usr/bin/env bash
PROJECT_NAME="ecentria-api-skeleton"
PROJECT_NAMESPACE="${PROJECT_NAME}-testing"
PROJECT_REPOSITORY="ssh://git@bitbucket.ecentria.tools/svc/ecentria-api-skeleton.git"
MINIO_PATH_TO_BINARY="/usr/bin/minio_cli"
MINIO_PATH_CONFIG_DIR="/home/dev/.minio_cli/"
PLAN_BUILD_KEY="EAS"
IMAGE_TAG="$(echo ${BRANCH} | grep -Eo '^([a-zA-Z0-9]+)-([0-9]+)')"

if [ -z "${IMAGE_TAG}" ];
    then
        IMAGE_TAG+=$(echo ${BRANCH} | cut -c -120)
    else
        IMAGE_TAG+="-"$(echo ${SHORT_PLAN_KEY} | sed 's/'${PLAN_BUILD_KEY}'//')
fi
