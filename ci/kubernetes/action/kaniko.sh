#!/usr/bin/env bash

DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
source ${DIR}/../../settings.sh

set -o nounset
set -o errexit
set -o pipefail

# Validate needed values
declare -a ENV_VARS=(
    "ROOT_DIR"
    "BUILD_KEY"
    "BUILD_COMMIT"
    "REGISTRY_HOST"
)

for VAR in "${ENV_VARS[@]}"; do
    if [[ ! -v ${VAR} ]]; then
        echo "${VAR} must be defined."
        exit 1
    fi
done

POD_LABEL="kaniko-${BUILD_KEY,,}"
POD_FILENAME="kaniko.yml"

COMMAND="git clone ssh://git@bitbucket.ecentria.tools/svc/ecentria-api-skeleton.git /api-skeleton-php"
COMMAND+=" && cd /api-skeleton-php"
COMMAND+=" && git fetch origin"
COMMAND+=" && git reset --hard ${BUILD_COMMIT}"
echo ">>> Preparing Kubernetes environment..."
export POD_LABEL
export REGISTRY_HOST
export PROJECT_NAME
export MINIO_PATH_TO_BINARY
export MINIO_PATH_CONFIG_DIR
export COMMAND
export IMAGE_TAG

 envsubst < "${ROOT_DIR}/ci/kubernetes/pod/${POD_FILENAME}" > manifest.yml && cat manifest.yml && kubectl create --namespace="${PROJECT_NAMESPACE}" -f manifest.yml

# Get build host (waiting for 10 min (120 tries * 5 sec))
echo "Waiting for Pod to start..."
kubectl wait --namespace="${PROJECT_NAMESPACE}" --timeout=10m --for=condition=Ready pod/"${POD_LABEL}"

echo ">>> Outputting Pod logs..."
# Get app container output (waiting for 5 min (60 tries * 5 sec))
function print_logs () {
  n=0;
  until [ $n -ge 60 ]; do
    kubectl --namespace="${PROJECT_NAMESPACE}" logs -f pod/"${POD_LABEL}" -c kaniko 2>&1 && return;
    (( n++ ))
    sleep 5;
  done;

  echo "Failed to get logs of Pod \"${POD_LABEL}\"."
  exit 1
}

print_logs;

echo -e "\n>>> Checking Pod Phase\n"
for ((i=0; i<10; i++)); do
    podPhase=$(kubectl get pods "${POD_LABEL}" -n "${PROJECT_NAMESPACE}" -o jsonpath="{.status.phase}")
    if [[ $podPhase == Succeeded ]]; then break; fi
    sleep 1
done

if [[ $podPhase != Succeeded ]]; then
    echo -e "TESTING FAILED\nPod \"${POD_LABEL}\" did not succeed" >&2;
    exit 1;
fi
exit 0
