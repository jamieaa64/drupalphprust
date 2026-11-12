# 02. Static references

Status: done

Views reference binding and finally handling pass regression checks.

## Work

- Preserve aliases when static storage is rebound.
- Keep the fix and fixture independently reviewable.

## Test / finish

- Compare the static-property-reference probe with Zend.
- Verify finally executes and the returned alias still mutates its source.

[Overall plan](README.md) · [Previous](01-repository.md) · [Next](03-runtime.md) · [Evidence / guidance](../../../docs/receipts/2026-11-12-static-property-reference.md)
