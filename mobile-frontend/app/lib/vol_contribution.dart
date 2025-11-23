import 'package:app/main_layout.dart';
import 'package:app/providers/contribution_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:app/models/contribution.dart';

class VolContribution extends StatefulWidget {
  final int userId;
  const VolContribution({super.key, required this.userId});

  @override
  State<VolContribution> createState() => _VolContributionState();
}

class _VolContributionState extends State<VolContribution> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final provider = Provider.of<ContributionProvider>(context, listen: false);
      await provider.getVolContributions(userId: widget.userId);
    });
  }
  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<ContributionProvider>(context);
    final contributions = provider.contributions;
    return MainLayout(
      child: SafeArea(
        child: SingleChildScrollView(
          padding: EdgeInsets.all(8),
          child: Column(
            children: [
              Card(
                color: Colors.blueGrey,
                child: Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.wallet_giftcard, color: Colors.white),
                      SizedBox(width: 10),
                      Text(
                        'Contributions',
                        style: TextStyle(color: Colors.white),
                      ),
                    ],
                  ),
                ),
              ),
              SizedBox(height: 16),
              contributions.isEmpty
                  ? SizedBox(
                    width: double.infinity,
                    child: Card(
                        margin: EdgeInsets.symmetric(vertical: 10),
                        elevation: 4,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                        color: Colors.amber.shade50,
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Text(
                            "No Contributions have been registered yet.",
                            style: TextStyle(
                              fontStyle: FontStyle.italic,
                              color: Colors.black,
                            ),
                          ),
                        ),
                      ),
                  )
                  : Column(
                      children: contributions.map((contribution) {
                        return SizedBox(
                          width: double.infinity,
                          child: Card(
                            margin: EdgeInsets.symmetric(vertical: 10),
                            elevation: 4,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                            color: Colors.amber.shade50,
                            child: Padding(
                              padding: const EdgeInsets.all(16.0),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    contribution.name,
                                    style: TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.brown.shade800,
                                    ),
                                  ),
                                  SizedBox(height: 10),
                                  buildRichText("📅 Date: ", getDate(contribution.createdAt)),
                                  buildRichText("🏥 Relief Center: ", contribution.centerName),
                                  buildRichText("📦 Quantity: ", "${contribution.quantity} ${contribution.unit}"),
                                  buildRichText("📝 Description: ", contribution.description ?? 'N/A'),
                                  buildRichText("🔁 Type: ", getType(contribution.type)),                              ],
                              ),
                            ),
                          ),
                        );
                      }).toList(),
                    ),
            ],
          ),
        ),
      ),
    );
  }

  RichText buildRichText(String label, String value) {
    return RichText(
      text: TextSpan(
        style: TextStyle(color: Colors.grey.shade800),
        children: [
          TextSpan(
            text: label,
            style: TextStyle(fontWeight: FontWeight.bold),
          ),
          TextSpan(text: value),
        ],
      ),
    );
  }
}